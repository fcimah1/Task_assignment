<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>استيراد الملفات - نظام إدارة المهام</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            padding: 40px;
            max-width: 600px;
            width: 100%;
        }
        
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 10px;
            font-size: 28px;
        }
        
        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
        }
        
        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            display: none;
        }
        
        .alert.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            display: block;
        }
        
        .alert.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            display: block;
        }
        
        .alert.info {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
            display: block;
        }
        
        .upload-area {
            border: 2px dashed #667eea;
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background-color: #f8f9ff;
            margin-bottom: 20px;
        }
        
        .upload-area:hover {
            background-color: #f0f2ff;
            border-color: #764ba2;
        }
        
        .upload-area.dragover {
            background-color: #e8ebff;
            border-color: #764ba2;
        }
        
        .upload-icon {
            font-size: 48px;
            margin-bottom: 10px;
        }
        
        .upload-text {
            color: #667eea;
            font-weight: 500;
            margin-bottom: 5px;
        }
        
        .upload-subtext {
            color: #999;
            font-size: 13px;
        }
        
        input[type="file"] {
            display: none;
        }
        
        .file-info {
            background: #f8f9ff;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            display: none;
        }
        
        .file-info.show {
            display: block;
        }
        
        .file-name {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
            word-break: break-all;
        }
        
        .file-size {
            color: #666;
            font-size: 13px;
            margin-bottom: 8px;
        }
        
        .progress-container {
            display: none;
            margin-bottom: 20px;
        }
        
        .progress-container.show {
            display: block;
        }
        
        .progress-label {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            color: #666;
            font-size: 13px;
        }
        
        .progress-bar {
            width: 100%;
            height: 8px;
            background: #e9ecef;
            border-radius: 10px;
            overflow: hidden;
        }
        
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #667eea, #764ba2);
            width: 0%;
            transition: width 0.3s ease;
        }
        
        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        
        button {
            flex: 1;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-upload {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-upload:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }
        
        .btn-upload:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }
        
        .btn-cancel {
            background: #e9ecef;
            color: #666;
        }
        
        .btn-cancel:hover {
            background: #dee2e6;
        }
        
        .status-message {
            text-align: center;
            margin-top: 15px;
            font-size: 13px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📤 استيراد الملفات</h1>
        <p class="subtitle">قم بتحميل ملفات Excel أو CSV</p>
        
        @if ($errors->any())
            <div class="alert error">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif
        
        @if (session('success'))
            <div class="alert success">{{ session('success') }}</div>
        @endif
        
        <div id="uploadArea" class="upload-area">
            <div class="upload-icon">📁</div>
            <div class="upload-text">اختر ملفاً أو اسحبه هنا</div>
            <div class="upload-subtext">الصيغ المدعومة: .xlsx, .xls, .csv (بدون حد أقصى للحجم)</div>
        </div>
        
        <div id="fileInfo" class="file-info">
            <div class="file-name" id="fileName"></div>
            <div class="file-size" id="fileSize"></div>
        </div>
        
        <div id="progressContainer" class="progress-container">
            <div class="progress-label">
                <span>جاري التحميل...</span>
                <span id="progressText">0%</span>
            </div>
            <div class="progress-bar">
                <div id="progressFill" class="progress-fill"></div>
            </div>
            <div class="status-message" id="statusMessage"></div>
        </div>
        
        <div class="button-group">
            <button type="button" id="uploadBtn" class="btn-upload" disabled>رفع الملف</button>
            <button type="button" id="cancelBtn" class="btn-cancel">إلغاء</button>
        </div>
    </div>

    <input type="file" id="fileInput" accept=".xlsx,.xls,.csv">

    <script>
        const CHUNK_SIZE = 1024 * 1024; // 1MB chunks
        const uploadArea = document.getElementById('uploadArea');
        const fileInput = document.getElementById('fileInput');
        const uploadBtn = document.getElementById('uploadBtn');
        const cancelBtn = document.getElementById('cancelBtn');
        const fileInfo = document.getElementById('fileInfo');
        const progressContainer = document.getElementById('progressContainer');
        const fileNameEl = document.getElementById('fileName');
        const fileSizeEl = document.getElementById('fileSize');
        const progressFill = document.getElementById('progressFill');
        const progressText = document.getElementById('progressText');
        const statusMessage = document.getElementById('statusMessage');
        
        let selectedFile = null;
        let uploadInProgress = false;
        let uploadSessionId = null;

        // File selection via click
        uploadArea.addEventListener('click', () => fileInput.click());

        // File selection via drag and drop
        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });

        uploadArea.addEventListener('dragleave', () => {
            uploadArea.classList.remove('dragover');
        });

        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                handleFileSelect(files[0]);
            }
        });

        // File input change
        fileInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                handleFileSelect(e.target.files[0]);
            }
        });

        function handleFileSelect(file) {
            const validTypes = ['application/vnd.ms-excel', 
                              'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                              'text/csv'];
            
            if (!validTypes.includes(file.type) && !file.name.match(/\.(xlsx|xls|csv)$/i)) {
                alert('الرجاء اختيار ملف Excel أو CSV');
                return;
            }

            selectedFile = file;
            uploadSessionId = Date.now().toString();
            
            fileNameEl.textContent = '📄 ' + file.name;
            fileSizeEl.textContent = 'الحجم: ' + (file.size / (1024 * 1024)).toFixed(2) + ' MB';
            fileInfo.classList.add('show');
            uploadBtn.disabled = false;
        }

        uploadBtn.addEventListener('click', startUpload);
        cancelBtn.addEventListener('click', resetUpload);

        async function startUpload() {
            if (!selectedFile) return;

            uploadInProgress = true;
            uploadBtn.disabled = true;
            cancelBtn.disabled = false;
            progressContainer.classList.add('show');

            const totalChunks = Math.ceil(selectedFile.size / CHUNK_SIZE);
            let uploadedChunks = 0;

            try {
                for (let i = 0; i < totalChunks; i++) {
                    const start = i * CHUNK_SIZE;
                    const end = Math.min(start + CHUNK_SIZE, selectedFile.size);
                    const chunk = selectedFile.slice(start, end);

                    await uploadChunk(chunk, i, totalChunks);
                    uploadedChunks++;

                    // Update progress
                    const progress = Math.round((uploadedChunks / totalChunks) * 100);
                    progressFill.style.width = progress + '%';
                    progressText.textContent = progress + '%';
                    statusMessage.textContent = `تم تحميل ${uploadedChunks} من ${totalChunks} جزء`;
                }

                // Finalize upload
                await finalizeUpload();
                
            } catch (error) {
                console.error('Upload error:', error);
                statusMessage.textContent = 'خطأ في التحميل: ' + error.message;
                resetUpload();
            }
        }

        function uploadChunk(chunk, chunkIndex, totalChunks) {
            return new Promise((resolve, reject) => {
                const formData = new FormData();
                formData.append('file', chunk);
                formData.append('chunkIndex', chunkIndex);
                formData.append('totalChunks', totalChunks);
                formData.append('sessionId', uploadSessionId);
                formData.append('fileName', selectedFile.name);
                formData.append('_token', '{{ csrf_token() }}');

                fetch('/import/chunk-upload', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (!response.ok) throw new Error(`HTTP ${response.status}`);
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        resolve();
                    } else {
                        reject(new Error(data.message || 'Chunk upload failed'));
                    }
                })
                .catch(reject);
            });
        }

        async function finalizeUpload() {
            const response = await fetch('/import/finalize-upload', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    sessionId: uploadSessionId,
                    fileName: selectedFile.name
                })
            });

            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            
            const data = await response.json();
            
            if (data.success) {
                progressContainer.classList.remove('show');
                fileInfo.classList.remove('show');
                
                const alert = document.createElement('div');
                alert.className = 'alert success';
                alert.innerHTML = '✓ تم رفع الملف بنجاح وبدء المعالجة';
                document.querySelector('.container').insertBefore(alert, document.querySelector('h1').nextElementSibling);
                
                setTimeout(() => {
                    location.reload();
                }, 2000);
            } else {
                throw new Error(data.message || 'Finalization failed');
            }
        }

        function resetUpload() {
            selectedFile = null;
            uploadSessionId = null;
            uploadInProgress = false;
            fileInput.value = '';
            fileInfo.classList.remove('show');
            progressContainer.classList.remove('show');
            progressFill.style.width = '0%';
            progressText.textContent = '0%';
            uploadBtn.disabled = true;
            cancelBtn.disabled = true;
        }
    </script>
</body>
</html>
