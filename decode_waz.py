import base64
import re

file50 = r'C:\Users\LENOVO\.gemini\antigravity-ide\brain\0a857e24-b013-40c0-96db-8d537cedd99c\.system_generated\steps\50\content.md'
file51 = r'C:\Users\LENOVO\.gemini\antigravity-ide\brain\0a857e24-b013-40c0-96db-8d537cedd99c\.system_generated\steps\51\content.md'

def decode_github_api(filepath, label):
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()
    match = re.search(r'"content":"([\s\S]+?)","encoding"', content)
    if match:
        b64 = match.group(1).replace('\n', '').replace(' ', '').replace('\\n', '')
        # Add padding
        pad = len(b64) % 4
        if pad:
            b64 += '=' * (4 - pad)
        decoded = base64.b64decode(b64).decode('utf-8', errors='replace')
        print(f'=== {label} ===')
        print(decoded)
        print()
    else:
        print(f'No content found in {filepath}')
        print(content[:2000])

decode_github_api(file50, 'z-score-weight-for-age.R')
decode_github_api(file51, 'z-score-length-for-age.R')
