// Cloudflare Email Worker untuk Forward Email ke Laravel
// Deploy di: Cloudflare Dashboard → Workers & Pages → Create Worker

export default {
  async email(message, env, ctx) {
    // URL webhook Laravel Bizmark.id
    const webhookUrl = 'https://bizmark.id/webhook/email/receive';
    
    try {
      console.log('Processing email from:', message.from, 'to:', message.to);
      
      // Get email content
      const textContent = await message.text();
      const htmlContent = await message.html();
      
      // Prepare email data untuk Laravel
      const emailData = {
        message_id: message.headers.get('message-id'),
        from: message.from,
        to: message.to,
        subject: message.headers.get('subject'),
        text: textContent,
        html: htmlContent,
        date: message.headers.get('date'),
        // Optional: tambahkan headers lain jika perlu
        reply_to: message.headers.get('reply-to'),
        cc: message.headers.get('cc'),
        bcc: message.headers.get('bcc'),
      };

      console.log('Forwarding email to Laravel webhook:', emailData.subject);

      // Send ke Laravel webhook
      const response = await fetch(webhookUrl, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-Cloudflare-Email-Worker': 'true',
          'User-Agent': 'Cloudflare-Email-Worker/1.0',
        },
        body: JSON.stringify(emailData)
      });

      const responseText = await response.text();
      
      if (response.ok) {
        console.log('✅ Email forwarded successfully:', responseText);
      } else {
        console.error('❌ Failed to forward email. Status:', response.status);
        console.error('Response:', responseText);
      }

      // Optional: Forward juga ke email backup
      // await message.forward('backup@bizmark.id');

    } catch (error) {
      console.error('❌ Worker error:', error.message);
      console.error('Stack:', error.stack);
      
      // Jangan throw error agar email tetap diterima Cloudflare
      // (akan masuk catch-all atau rejected)
    }
  }
}
