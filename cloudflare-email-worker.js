// Cloudflare Email Worker untuk Forward Email ke Laravel
// Deploy di: Cloudflare Dashboard → Workers & Pages → Create Worker
// Simplified version - Accept email first, then forward

export default {
  // Email handler - Main function untuk handle incoming email
  async email(message, env, ctx) {
    // URL webhook Laravel Bizmark.id
    const webhookUrl = 'https://bizmark.id/webhook/email/receive';
    
    // Get basic info immediately (synchronous)
    const from = message.from;
    const to = message.to;
    const subject = message.headers.get('subject') || '(No subject)';
    const messageId = message.headers.get('message-id') || 'unknown';
    
    console.log('📧 Email received from:', from, 'to:', to);
    
    // Forward message to Laravel in background
    ctx.waitUntil(
      (async () => {
        try {
          // Get email content - Use raw() and parse manually
          let textContent = '';
          let htmlContent = '';
          
          console.log('🔍 Extracting email content...');
          
          // Get raw email content
          try {
            const rawStream = message.raw;
            const reader = rawStream.getReader();
            const chunks = [];
            
            while (true) {
              const { done, value } = await reader.read();
              if (done) break;
              chunks.push(value);
            }
            
            // Combine chunks
            const totalLength = chunks.reduce((acc, chunk) => acc + chunk.length, 0);
            const rawBytes = new Uint8Array(totalLength);
            let offset = 0;
            for (const chunk of chunks) {
              rawBytes.set(chunk, offset);
              offset += chunk.length;
            }
            
            const rawContent = new TextDecoder().decode(rawBytes);
            console.log('✅ Got raw email:', rawContent.length, 'bytes');
            
            // Helper function to decode quoted-printable
            function decodeQuotedPrintable(str) {
              return str
                .replace(/=\r?\n/g, '') // Remove soft line breaks
                .replace(/=([0-9A-F]{2})/gi, (match, hex) => String.fromCharCode(parseInt(hex, 16)));
            }
            
            // Helper function to decode HTML entities
            function decodeHtmlEntities(str) {
              return str
                .replace(/=3D/g, '=')
                .replace(/&quot;/g, '"')
                .replace(/&amp;/g, '&')
                .replace(/&lt;/g, '<')
                .replace(/&gt;/g, '>')
                .replace(/&#(\d+);/g, (match, dec) => String.fromCharCode(dec))
                .replace(/=C2=A0/g, ' '); // Non-breaking space
            }
            
            // Parse MIME multipart email properly
            const headerEndIndex = rawContent.indexOf('\r\n\r\n');
            if (headerEndIndex === -1) {
              console.warn('⚠️ Could not find header/body separator');
              textContent = rawContent;
            } else {
              const headers = rawContent.substring(0, headerEndIndex);
              const body = rawContent.substring(headerEndIndex + 4);
              
              // Check if multipart
              const contentTypeMatch = headers.match(/Content-Type:\s*([^;\r\n]+)/i);
              const boundaryMatch = headers.match(/boundary="?([^"\r\n]+)"?/i);
              
              if (contentTypeMatch && contentTypeMatch[1].includes('multipart') && boundaryMatch) {
                const boundary = boundaryMatch[1];
                console.log('🔍 Parsing multipart email with boundary:', boundary);
                
                // Split by boundary
                const parts = body.split('--' + boundary);
                
                for (const part of parts) {
                  if (part.trim() === '' || part.trim() === '--') continue;
                  
                  // Split part into headers and content
                  const partHeaderEnd = part.indexOf('\r\n\r\n');
                  if (partHeaderEnd === -1) continue;
                  
                  const partHeaders = part.substring(0, partHeaderEnd);
                  let partContent = part.substring(partHeaderEnd + 4).trim();
                  
                  // Check encoding
                  const isQuotedPrintable = partHeaders.match(/Content-Transfer-Encoding:\s*quoted-printable/i);
                  
                  // Decode if needed
                  if (isQuotedPrintable) {
                    partContent = decodeQuotedPrintable(partContent);
                    console.log('🔓 Decoded quoted-printable content');
                  }
                  
                  // Decode HTML entities
                  partContent = decodeHtmlEntities(partContent);
                  
                  // Check content type of this part
                  if (partHeaders.match(/Content-Type:\s*text\/plain/i)) {
                    textContent = partContent;
                    console.log('✅ Found text/plain part:', textContent.length, 'chars');
                  } else if (partHeaders.match(/Content-Type:\s*text\/html/i)) {
                    htmlContent = partContent;
                    console.log('✅ Found text/html part:', htmlContent.length, 'chars');
                  }
                }
                
                // If no text found, extract from HTML
                if (!textContent && htmlContent) {
                  // Strip HTML tags for plain text
                  textContent = htmlContent
                    .replace(/<style[^>]*>[\s\S]*?<\/style>/gi, '')
                    .replace(/<script[^>]*>[\s\S]*?<\/script>/gi, '')
                    .replace(/<[^>]+>/g, '')
                    .replace(/\s+/g, ' ')
                    .trim();
                  console.log('✅ Extracted text from HTML:', textContent.length, 'chars');
                }
              } else {
                // Single part email
                console.log('📄 Single part email (not multipart)');
                
                // Check if quoted-printable
                const isQuotedPrintable = headers.match(/Content-Transfer-Encoding:\s*quoted-printable/i);
                let decodedBody = body;
                
                if (isQuotedPrintable) {
                  decodedBody = decodeQuotedPrintable(body);
                  console.log('🔓 Decoded quoted-printable single part');
                }
                
                // Decode HTML entities
                decodedBody = decodeHtmlEntities(decodedBody);
                
                if (decodedBody.includes('<html') || decodedBody.includes('<HTML')) {
                  htmlContent = decodedBody;
                  textContent = decodedBody
                    .replace(/<style[^>]*>[\s\S]*?<\/style>/gi, '')
                    .replace(/<script[^>]*>[\s\S]*?<\/script>/gi, '')
                    .replace(/<[^>]+>/g, '')
                    .replace(/\s+/g, ' ')
                    .trim();
                  console.log('✅ Found HTML in single part:', htmlContent.length, 'chars');
                } else {
                  textContent = decodedBody.trim();
                  console.log('✅ Found plain text:', textContent.length, 'chars');
                }
              }
            }
            
          } catch (e) {
            console.error('❌ Error extracting content:', e.message);
            textContent = '[Error: Could not extract email content - ' + e.message + ']';
          }
          
          // Prepare payload
          const payload = {
            message_id: messageId,
            from: from,
            to: to,
            subject: subject,
            text: textContent || '[No text content]',
            html: htmlContent || null,
            date: message.headers.get('date') || new Date().toISOString(),
            reply_to: message.headers.get('reply-to'),
            cc: message.headers.get('cc'),
            bcc: message.headers.get('bcc'),
          };
          
          console.log('🚀 Sending to webhook:', JSON.stringify(payload).length, 'bytes');

          // Send to Laravel
          const response = await fetch(webhookUrl, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-Cloudflare-Email-Worker': 'true',
            },
            body: JSON.stringify(payload)
          });

          const responseText = await response.text();
          
          if (response.ok) {
            console.log('✅ Forwarded successfully');
            console.log('📥 Response:', responseText);
          } else {
            console.error('❌ Forward failed. Status:', response.status);
            console.error('📛 Response:', responseText);
          }
        } catch (error) {
          console.error('❌ Critical error:', error.message);
          console.error('📚 Stack:', error.stack);
        }
      })()
    );
    
    // Accept email immediately by returning void
  },

  // Fetch handler - Optional, untuk test worker via HTTP
  async fetch(request, env, ctx) {
    return new Response(JSON.stringify({
      status: 'ok',
      message: 'Cloudflare Email Worker is running',
      note: 'This worker handles incoming emails for cs@bizmark.id',
      endpoint: 'https://bizmark.id/webhook/email/receive',
      email_handler: 'active'
    }), {
      status: 200,
      headers: {
        'Content-Type': 'application/json',
      }
    });
  }
}
