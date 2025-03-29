<!DOCTYPE html>
<html>
<head>
    <title>RocketChat Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding: 20px; }
        .hidden { display: none; }
        .visible { display: block; }
    </style>
</head>
<body>
    <div class="container">
        <h1>RocketChat Test Page</h1>

        <div class="card mb-4">
            <div class="card-header">
                <h3>Test Toggle Visibility</h3>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="test-select" class="form-label">Select an option:</label>
                    <select id="test-select" class="form-control" onchange="toggleOptions()">
                        <option value="">-- Select option --</option>
                        <option value="option1">Option 1</option>
                        <option value="option2">Option 2</option>
                    </select>
                </div>

                <div id="option1-content" class="hidden mt-3 p-3 bg-light">
                    <h4>Option 1 Content</h4>
                    <p>This content should appear when Option 1 is selected.</p>
                </div>

                <div id="option2-content" class="hidden mt-3 p-3 bg-light">
                    <h4>Option 2 Content</h4>
                    <p>This content should appear when Option 2 is selected.</p>
                    <input type="text" class="form-control" placeholder="Test input field">
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h3>Browser Information</h3>
            </div>
            <div class="card-body">
                <div id="browser-info"></div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h3>Console Output</h3>
            </div>
            <div class="card-body">
                <pre id="console-output" class="bg-dark text-light p-3" style="min-height: 200px; overflow-y: auto;"></pre>
            </div>
        </div>
    </div>

    <script>
        // Intercept console.log for display
        const originalConsoleLog = console.log;
        console.log = function() {
            const output = document.getElementById('console-output');
            const args = Array.from(arguments);
            const message = args.map(arg =>
                typeof arg === 'object' ? JSON.stringify(arg, null, 2) : String(arg)
            ).join(' ');

            output.textContent += message + '\n';
            originalConsoleLog.apply(console, arguments);
        };

        // Get browser info
        function getBrowserInfo() {
            const browserInfo = document.getElementById('browser-info');
            browserInfo.innerHTML = `
                <p><strong>User Agent:</strong> ${navigator.userAgent}</p>
                <p><strong>Browser:</strong> ${navigator.appName}</p>
                <p><strong>Platform:</strong> ${navigator.platform}</p>
                <p><strong>Screen Width:</strong> ${window.screen.width}</p>
                <p><strong>Window Width:</strong> ${window.innerWidth}</p>
            `;
        }

        // Toggle options visibility
        function toggleOptions() {
            console.log('Toggle function called');
            const select = document.getElementById('test-select');
            const option1Content = document.getElementById('option1-content');
            const option2Content = document.getElementById('option2-content');

            console.log('Selected value:', select.value);

            // Hide both options
            option1Content.className = 'hidden mt-3 p-3 bg-light';
            option2Content.className = 'hidden mt-3 p-3 bg-light';

            // Show the selected option
            if (select.value === 'option1') {
                option1Content.className = 'visible mt-3 p-3 bg-light';
                console.log('Showing Option 1 content');
            } else if (select.value === 'option2') {
                option2Content.className = 'visible mt-3 p-3 bg-light';
                console.log('Showing Option 2 content');
            }
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded');
            getBrowserInfo();

            const testSelect = document.getElementById('test-select');
            testSelect.addEventListener('change', function() {
                console.log('Select changed to:', this.value);
            });

            console.log('Test page ready');
        });
    </script>
</body>
</html>
