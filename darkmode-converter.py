#!/usr/bin/env python3
"""
Dark Mode Converter - Mengubah Blade templates ke Apple HIG Dark Mode
"""
import re
import sys

def convert_to_dark_mode(content):
    """Convert light mode classes to dark mode"""
    
    # Card containers
    content = re.sub(
        r'class="bg-white rounded(?:-lg|-apple-lg)?\s+(?:shadow-sm|shadow-apple)?\s*border(?:\s+border-gray-100)?\s*(.*?)"',
        r'class="card-elevated rounded-apple-lg \1"',
        content
    )
    
    # Header sections dengan border
    content = re.sub(
        r'class="([^"]*?)border-b\s+border-gray-(?:100|200)([^"]*?)"',
        r'class="\1\2" style="border-bottom: 1px solid rgba(84, 84, 88, 0.65);"',
        content
    )
    
    # Headings (h1, h2, h3, h4) text-gray-XXX
    content = re.sub(
        r'class="([^"]*?)text-(?:gray-(?:800|900)|black)([^"]*?)"([^>]*?)>([^<]*?)</h([1-4])>',
        r'class="\1\2" style="color: #FFFFFF;"\3>\4</h\5>',
        content
    )
    
    # Title text dalam tag lain
    content = re.sub(
        r'class="([^"]*?)text-lg\s+font-semibold\s+text-(?:gray-(?:800|900)|black)([^"]*?)"',
        r'class="\1text-lg font-semibold\2" style="color: #FFFFFF;"',
        content
    )
    
    # Description/subtitle text
    content = re.sub(
        r'class="([^"]*?)text-(?:gray-600|apple-gray)([^"]*?)"',
        r'class="\1\2" style="color: rgba(235, 235, 245, 0.6);"',
        content
    )
    
    # Labels
    content = re.sub(
        r'class="([^"]*?)text-(?:gray-700)([^"]*?)"',
        r'class="\1\2" style="color: rgba(235, 235, 245, 0.8);"',
        content
    )
    
    # Input fields - bg-apple-light-gray atau border
    content = re.sub(
        r'class="([^"]*?)(?:bg-apple-light-gray|border\s+border-gray-300)([^"]*?)(?:focus:ring-2\s+focus:ring-(?:blue-500|apple-blue))?([^"]*?)"',
        r'class="\1input-dark\2\3"',
        content
    )
    
    # Remove focus:bg-white
    content = content.replace('focus:bg-white transition-apple', 'transition-apple')
    content = content.replace('focus:bg-white', '')
    
    # Error text
    content = re.sub(
        r'text-red-(?:500|600)',
        r'text-apple-red-dark',
        content
    )
    
    # Required markers
    content = re.sub(
        r'class="text-red-500"',
        r'class="text-apple-red-dark"',
        content
    )
    
    # Link colors
    content = re.sub(
        r'text-blue-600\s+hover:text-blue-800',
        r'text-apple-blue-dark hover:text-apple-blue',
        content
    )
    
    # Buttons - primary action
    content = re.sub(
        r'class="([^"]*?)bg-(?:blue-600|apple-blue)\s+text-white([^"]*?)hover:bg-blue-(?:700|600)([^"]*?)"',
        r'class="\1btn-primary\2\3"',
        content
    )
    
    # Cancel/secondary buttons
    content = re.sub(
        r'class="([^"]*?)bg-gray-(?:100|200)\s+text-(?:gray-700|apple-gray)([^"]*?)"',
        r'class="\1\2" style="background: rgba(58, 58, 60, 0.8); color: rgba(235, 235, 245, 0.8);"',
        content
    )
    
    # Badge/Chip backgrounds
    content = re.sub(
        r'class="([^"]*?)bg-(?:gray-100|blue-100|green-100|red-100|yellow-100)([^"]*?)"',
        r'class="\1\2" style="background: rgba(58, 58, 60, 0.6);"',
        content
    )
    
    # Dark text untuk display values
    content = re.sub(
        r'class="([^"]*?)text-gray-900([^"]*?)"',
        r'class="\1\2" style="color: rgba(235, 235, 245, 0.8);"',
        content
    )
    
    # Clean up double spaces in classes
    content = re.sub(r'\s+', ' ', content)
    content = re.sub(r'class="\s+', 'class="', content)
    content = re.sub(r'\s+"', '"', content)
    
    return content

def main():
    if len(sys.argv) < 2:
        print("Usage: python3 darkmode-converter.py <file>")
        sys.exit(1)
    
    filepath = sys.argv[1]
    
    try:
        with open(filepath, 'r', encoding='utf-8') as f:
            content = f.read()
        
        converted = convert_to_dark_mode(content)
        
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(converted)
        
        print(f"✓ Converted {filepath} to dark mode")
        
    except Exception as e:
        print(f"✗ Error: {e}")
        sys.exit(1)

if __name__ == '__main__':
    main()
