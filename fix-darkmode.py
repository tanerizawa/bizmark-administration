#!/usr/bin/env python3
"""
Fix Dark Mode - Clean up sed replacement errors
"""
import re
import sys
import glob

def fix_darkmode_file(filepath):
    """Fix syntax errors from sed replacements"""
    
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # Fix double quotes at end
    content = re.sub(r'style="([^"]+)""', r'style="\1"', content)
    
    # Fix class with embedded style
    content = re.sub(
        r'class="([^"]*?)style="([^"]+)"([^"]*?)"',
        r'class="\1\3" style="\2"',
        content
    )
    
    # Fix class with trailing attribute without class
    content = re.sub(r' (mb|pb|mt|pt|px|py|pl|pr)-(\d+)"', r' class="\1-\2"', content)
    
    # Fix standalone mb-/pb- etc
    content = re.sub(r' (mb|pb|mt|pt|px|py|pl|pr)-(\d+)([^"])', r' class="\1-\2"\3', content)
    
    # Fix text-apple-red-dark-dark
    content = content.replace('text-apple-red-dark-dark', 'text-apple-red-dark')
    content = content.replace('text-apple-blue-dark-dark', 'text-apple-blue-dark')
    
    # Fix double spaces in class names
    content = re.sub(r'class="([^"]*)\s{2,}([^"]*)"', r'class="\1 \2"', content)
    
    # Remove empty class attributes
    content = content.replace('class=""', '')
    
    # Fix orphaned style attributes merged into class
    patterns = [
        (r'(text|font|border)-(\w+)\s+style=', r'class="\1-\2" style='),
        (r'(text|font|border)-(\w+)-(\w+)\s+style=', r'class="\1-\2-\3" style='),
    ]
    
    for pattern, replacement in patterns:
        content = re.sub(pattern, replacement, content)
    
    with open(filepath, 'w', encoding='utf-8') as f:
        f.write(content)
    
    return filepath

def main():
    patterns = [
        'resources/views/projects/*.blade.php',
        'resources/views/tasks/*.blade.php',
        'resources/views/documents/*.blade.php',
        'resources/views/institutions/*.blade.php',
    ]
    
    files_fixed = []
    for pattern in patterns:
        for filepath in glob.glob(pattern):
            if not filepath.endswith('.backup') and not filepath.endswith('_old.blade.php'):
                if any(x in filepath for x in ['create.blade.php', 'edit.blade.php', 'show.blade.php']):
                    fix_darkmode_file(filepath)
                    files_fixed.append(filepath)
                    print(f"✓ Fixed {filepath}")
    
    print(f"\n✓ Total {len(files_fixed)} files fixed")

if __name__ == '__main__':
    main()
