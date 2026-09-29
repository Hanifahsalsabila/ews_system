import os
import re

transcript_path = r'C:\Users\LENOVO\.gemini\antigravity-ide\brain\8efaf08b-abde-4047-867b-a9ba8c1884e9\.system_generated\logs\transcript_full.jsonl'
os.makedirs('storage/app/who_data', exist_ok=True)

with open(transcript_path, 'r', encoding='utf-8') as f:
    text = f.read()

# Replace literal \n with actual newlines to unescape JSON formatting
# Actually it's better to just parse it as raw text and split by \n or \r\n
# Wait, json escaping means \n is literally \ and n.
text = text.replace('\\n', '\n')

blocks = []
current_block = []
in_block = False

for line in text.split('\n'):
    line = line.strip()
    # Strip some JSON formatting if present like ending quotes
    if line.endswith('"') or line.endswith("',"):
        line = line.rstrip('",\'')
    
    if line.startswith('Height,L,M,S') or line.startswith('Length,L,M,S'):
        if current_block:
            blocks.append('\n'.join(current_block))
            current_block = []
        in_block = True
    
    if in_block:
        if not line or (',' not in line and current_block):
            blocks.append('\n'.join(current_block))
            current_block = []
            in_block = False
        elif line:
            current_block.append(line)

if current_block:
    blocks.append('\n'.join(current_block))

# Filter blocks that look like our CSVs
csv_blocks = [b for b in blocks if len(b.split('\n')) > 50 and 'SD3neg' in b]

# Take the last 4 blocks as they are the most recent ones from the user
csv_blocks = csv_blocks[-4:]

if len(csv_blocks) == 4:
    # Based on the text, order is:
    # 1. wfh_boys_2-to-5-years_zscores.csv
    # 2. wfh_girls_2-to-5-years_zscores.csv
    # 3. wfl_boys_0-to-2-years_zscores.csv
    # 4. wfl_girls_0-to-2-years_zscores.csv
    names = [
        'wfh_boys_2-to-5-years_zscores.csv',
        'wfh_girls_2-to-5-years_zscores.csv',
        'wfl_boys_0-to-2-years_zscores.csv',
        'wfl_girls_0-to-2-years_zscores.csv'
    ]
    for i, b in enumerate(csv_blocks):
        with open(f'storage/app/who_data/{names[i]}', 'w', encoding='utf-8') as f:
            f.write(b)
        print(f"Saved {names[i]} with {len(b.splitlines())} lines")
else:
    print(f"Found {len(csv_blocks)} blocks, expected 4.")
    for i, b in enumerate(csv_blocks):
        print(f"Block {i} has {len(b.splitlines())} lines")
