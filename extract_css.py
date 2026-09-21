import re
import os

files = [
    'resources/views/layouts/app.blade.php',
    'resources/views/admin/users/index.blade.php'
]

css_content = []

for file_path in files:
    with open(file_path, 'r', encoding='utf-8') as f:
        content = f.read()
    
    styles = re.findall(r'<style>(.*?)</style>', content, re.DOTALL)
    for style in styles:
        css_content.append(style.strip())
        
    # Remove the style blocks
    content = re.sub(r'<style>.*?</style>', '', content, flags=re.DOTALL)
    
    # If app.blade.php, inject link
    if 'app.blade.php' in file_path:
        content = content.replace('<!-- Chart.js for Dash UI Style Analytics -->\n    <script src=\"https://cdn.jsdelivr.net/npm/chart.js\"></script>', '<!-- Chart.js for Dash UI Style Analytics -->\n    <script src=\"https://cdn.jsdelivr.net/npm/chart.js\"></script>\n\n    <!-- Extracted Dashboard CSS -->\n    <link rel=\"stylesheet\" href=\"{{ asset(\'css/dashboard.css\') }}\">')
    
    with open(file_path, 'w', encoding='utf-8') as f:
        f.write(content)

os.makedirs('public/css', exist_ok=True)
with open('public/css/dashboard.css', 'w', encoding='utf-8') as f:
    f.write('\n\n'.join(css_content))

print('CSS extracted successfully.')
