import base64
import re

filepath = r'C:\Users\LENOVO\.gemini\antigravity-ide\brain\0a857e24-b013-40c0-96db-8d537cedd99c\.system_generated\steps\77\content.md'

with open(filepath, 'r', encoding='utf-8') as f:
    content = f.read()

match = re.search(r'"content":"([\s\S]+?)","encoding"', content)
if match:
    b64 = match.group(1).replace('\n', '').replace(' ', '').replace('\\n', '')
    pad = len(b64) % 4
    if pad:
        b64 += '=' * (4 - pad)
    decoded = base64.b64decode(b64).decode('utf-8', errors='replace')
    print('=== utils.R ===')
    print(decoded)
else:
    print('NOT FOUND')
    print(content[:2000])
