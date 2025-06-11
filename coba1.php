<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>File Processing Progress</title>
    <style>
        #progress-bar {
            width: 100%;
            background: #ddd;
        }
        #progress {
            width: 0%;
            background: green;
            height: 30px;
            text-align: center;
            color: white;
        }
    </style>
</head>
<body>
    <h2>Processing File...</h2>
    <div id="progress-bar">
        <div id="progress">0%</div>
    </div>
    <p id="execution-time"></p>

    <script>
        // Create a new EventSource to listen for updates
        const eventSource = new EventSource("testrename.php");

        eventSource.onmessage = function(event) {
            const data = JSON.parse(event.data);
            
            if (data.progress) {
                document.getElementById("progress").style.width = data.progress + "%";
                document.getElementById("progress").innerText = Math.round(data.progress) + "%";
            }

            if (data.status === "completed") {
                eventSource.close();
                document.getElementById("execution-time").innerText = "Execution Time: " + data.execution_time + " seconds";
            }
        };

        eventSource.onerror = function() {
            console.error("Error receiving updates. Check server.");
            eventSource.close();
        };
    </script>
</body>
</html>
