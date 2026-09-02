<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $book->title }} - خواننده کتاب</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: '#3b82f6',
                        secondary: '#1e40af',
                        paper: '#f5f5f4',
                        darkPaper: '#1e293b',
                        sepiaPaper: '#f5ebd9',
                        greenPaper: '#e6f4ea'
                    }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Vazirmatn:wght@100;200;300;400;500;600;700;800;900&display=swap');
        
        * {
            font-family: 'Vazirmatn', sans-serif;
            transition: background-color 0.3s, color 0.3s;
        }
        
        body {
            background-color: #f3f4f6;
            overflow-x: hidden;
        }
        
        .dark body {
            background-color: #0f172a;
        }
        
        .sepia body {
            background-color: #f0e6d2;
        }
        
        .green body {
            background-color: #e1f5e4;
        }
        
        .page {
            width: 210mm;
            min-height: 297mm;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            position: relative;
        }
        
        .progress-bar {
            height: 5px;
            background: linear-gradient(to right, #3b82f6, #8b5cf6);
        }
        
        .highlight {
            background-color: rgba(255, 255, 0, 0.4);
            border-radius: 2px;
        }
        
        .tooltip {
            position: relative;
        }
        
        .tooltip-text {
            visibility: hidden;
            width: 120px;
            background-color: #333;
            color: #fff;
            text-align: center;
            border-radius: 6px;
            padding: 5px;
            position: absolute;
            z-index: 1;
            bottom: 125%;
            left: 50%;
            transform: translateX(-50%);
            opacity: 0;
            transition: opacity 0.3s;
            font-size: 12px;
        }
        
        .tooltip:hover .tooltip-text {
            visibility: visible;
            opacity: 1;
        }
        
        .search-match {
            background-color: #fde047;
            border-radius: 2px;
            padding: 0 2px;
        }
        
        .current-match {
            background-color: #f59e0b;
            border: 2px solid #d97706;
            border-radius: 2px;
            padding: 0 2px;
        }
        
        .settings-panel {
            transition: transform 0.3s ease-in-out;
        }
        
        .font-option {
            cursor: pointer;
            padding: 8px;
            border-radius: 4px;
        }
        
        .font-option:hover {
            background-color: #e2e8f0;
        }
        
        .dark .font-option:hover {
            background-color: #334155;
        }
        
        .theme-option {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            margin: 0 5px;
            border: 2px solid transparent;
        }
        
        .theme-option.active {
            border-color: #3b82f6;
        }
        
        .page-content {
            white-space: pre-line;
        }
        
        /* Responsive Styles */
        @media (max-width: 768px) {
            .page {
                width: 100%;
                min-height: auto;
                padding: 1.5rem !important;
                margin-bottom: 2rem;
                border: 1px solid #e5e7eb;
                border-radius: 0.5rem;
            }
            
            header .container {
                /* flex-direction: column; */
                /* align-items: flex-start; */
                padding: 0.5rem;
            }
            
            header .flex {
                /* width: 100%; */
                /* justify-content: space-between; */
                margin-top: 0.25rem;
            }
            
            #searchPanel .container {
                flex-direction: column;
            }
            
            #searchInput {
                border-radius: 0.5rem;
                margin-bottom: 0.5rem;
            }
            
            #searchNext, #searchPrev, #closeSearch {
                width: 100%;
                border-radius: 0.5rem;
                margin: 0.25rem 0;
            }
            
            #pageNavigation {
                width: 95%;
                flex-wrap: wrap;
                padding: 8px;
                bottom: 1rem;
                font-size: 0.875rem;
            }
            
            #prevPage, #nextPage {
                padding: 4px;
            }
            
            #jumpToPage {
                width: 50px;
                margin: 0 4px;
                padding: 4px;
            }
            
            #goToPage {
                padding: 4px 8px;
                margin: 0 2px;
            }
            
            .settings-panel {
                width: 100%;
                right: 0;
                top: 4rem;
                height: calc(100vh - 4rem);
                overflow-y: auto;
            }
            
            .progress-bar {
                height: 3px;
            }
            
            #filename {
                max-width: 150px;
                font-size: 0.9rem;
            }
        }
        
        @media (max-width: 480px) {
            .page {
                box-shadow: none;
                padding: 1rem !important;
            }
            
            .dark .page {
                border-color: #374151;
            }
            
            #pageNavigation {
                width: 98%;
                font-size: 0.8rem;
                padding: 6px;
            }
            
            #currentPage, #totalPages {
                font-size: 0.8rem;
            }
            
            #jumpToPage {
                width: 40px;
            }
            
            #filename {
                max-width: 120px;
                font-size: 0.8rem;
            }
            
            header {
                /* padding: 0.25rem 0; */
            }
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <header class="fixed top-0 left-0 right-0 bg-white dark:bg-gray-800 shadow-md z-50">
        <div class="container mx-auto py-2 px-4 flex justify-between items-center">
            <div class="flex items-center">
                <i class="fas fa-book text-blue-500 text-xl mr-2"></i>
                <h1 id="filename" class="text-lg font-bold text-gray-800 dark:text-white truncate max-w-[200px] md:max-w-none">{{ $book->title }}</h1>
            </div>
            
            <div class="flex items-center space-x-2 md:space-x-4">
                <div class="tooltip">
                    <button id="searchBtn" class="p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700">
                        <i class="fas fa-search text-gray-600 dark:text-gray-300"></i>
                    </button>
                    <span class="tooltip-text">جستجو در کتاب</span>
                </div>
                
                <div class="tooltip">
                    <button id="settingsBtn" class="p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700">
                        <i class="fas fa-cog text-gray-600 dark:text-gray-300"></i>
                    </button>
                    <span class="tooltip-text">تنظیمات نمایش</span>
                </div>
                
                <div class="tooltip">
                    <button id="fullscreenBtn" class="p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700">
                        <i class="fas fa-expand text-gray-600 dark:text-gray-300"></i>
                    </button>
                    <span class="tooltip-text">حالت تمام صفحه</span>
                </div>
            </div>
        </div>
        <div class="progress-bar" id="progressBar"></div>
    </header>
    
    <!-- Search Panel -->
    <div id="searchPanel" class="fixed top-16 left-0 right-0 bg-white dark:bg-gray-800 shadow-md z-40 py-3 px-4 hidden">
        <div class="container mx-auto">
            <div class="flex flex-col">
                <input type="text" id="searchInput" placeholder="عبارت مورد نظر را جستجو کنید..." 
                    class="flex-grow px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary mb-2">
                
                <div class="flex flex-col space-y-2 md:flex-row md:space-y-0 md:space-x-2">
                    <button id="searchPrev" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                        قبلی <i class="fas fa-arrow-up ml-1"></i>
                    </button>
                    <button id="searchNext" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                        بعدی <i class="fas fa-arrow-down ml-1"></i>
                    </button>
                    <button id="closeSearch" class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-white px-4 py-2 rounded-lg">
                        <i class="fas fa-times"></i> بستن
                    </button>
                </div>
            </div>
            <div id="searchResults" class="mt-2 text-sm text-gray-600 dark:text-gray-400"></div>
        </div>
    </div>
    
    <!-- Settings Panel -->
    <div id="settingsPanel" class="settings-panel fixed top-16 right-0 h-[calc(100vh-4rem)] w-full md:w-80 bg-white dark:bg-gray-800 shadow-lg z-40 p-6 transform translate-x-full">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold text-gray-800 dark:text-white">تنظیمات نمایش</h2>
            <button id="closeSettings" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <div class="mb-6">
            <h3 class="font-semibold text-gray-700 dark:text-gray-300 mb-3">تم صفحه</h3>
            <div class="flex">
                <div class="theme-option bg-white active" data-theme="light"></div>
                <div class="theme-option bg-gray-800" data-theme="dark"></div>
                <div class="theme-option bg-amber-50" data-theme="sepia"></div>
                <div class="theme-option bg-green-50" data-theme="green"></div>
            </div>
        </div>
        
        <div class="mb-6">
            <h3 class="font-semibold text-gray-700 dark:text-gray-300 mb-3">اندازه فونت</h3>
            <div class="flex items-center">
                <button id="decreaseFont" class="p-2 bg-gray-200 dark:bg-gray-700 rounded-l-lg">
                    <i class="fas fa-minus"></i>
                </button>
                <span id="fontSizeDisplay" class="px-4 py-2 bg-gray-100 dark:bg-gray-700">16px</span>
                <button id="increaseFont" class="p-2 bg-gray-200 dark:bg-gray-700 rounded-r-lg">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
        </div>
        
        <div class="mb-6">
            <h3 class="font-semibold text-gray-700 dark:text-gray-300 mb-3">نوع فونت</h3>
            <div class="grid grid-cols-2 gap-2">
                <div class="font-option text-center border border-gray-300 dark:border-gray-600 rounded-lg" data-font="vazirmatn">
                    <span class="font-vazirmatn">وزیرمتن</span>
                </div>
                <div class="font-option text-center border border-gray-300 dark:border-gray-600 rounded-lg" data-font="tahoma">
                    <span class="font-tahoma">تهنا</span>
                </div>
                <div class="font-option text-center border border-gray-300 dark:border-gray-600 rounded-lg" data-font="arial">
                    <span class="font-arial">آریال</span>
                </div>
                <div class="font-option text-center border border-gray-300 dark:border-gray-600 rounded-lg" data-font="times">
                    <span class="font-times">تایمز</span>
                </div>
            </div>
        </div>
        
        <div class="mb-6">
            <h3 class="font-semibold text-gray-700 dark:text-gray-300 mb-3">فاصله خطوط</h3>
            <input type="range" id="lineSpacing" min="1" max="3" step="0.2" value="1.5" class="w-full">
            <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400">
                <span>فشرده</span>
                <span>معمولی</span>
                <span>گسترده</span>
            </div>
        </div>
    </div>
    
    <!-- Main Content -->
    <main class="container mx-auto pt-20 md:pt-24 pb-16 px-4">
        <div id="documentViewer" class="flex flex-col items-center">
            <!-- Pages will be dynamically added here -->
            <div class="text-center py-12">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto"></div>
                <p class="mt-4 text-gray-600">در حال بارگذاری کتاب...</p>
            </div>
        </div>
    </main>
    
    <!-- Page Navigation -->
    <div id="pageNavigation" class="fixed bottom-4 left-1/2 transform -translate-x-1/2 bg-white dark:bg-gray-800 rounded-full shadow-lg px-3 py-2 flex items-center z-30">
        <button id="prevPage" class="p-1 md:p-2 text-gray-600 dark:text-gray-300 hover:text-primary">
            <i class="fas fa-chevron-right text-sm md:text-base"></i>
        </button>
        <div class="px-2 md:px-4 flex items-center">
            <span id="currentPage" class="font-medium text-gray-800 dark:text-white text-sm md:text-base">1</span>
            <span class="mx-1 text-gray-500 text-sm md:text-base">از</span>
            <span id="totalPages" class="text-gray-600 dark:text-gray-300 text-sm md:text-base">?</span>
        </div>
        <button id="nextPage" class="p-1 md:p-2 text-gray-600 dark:text-gray-300 hover:text-primary">
            <i class="fas fa-chevron-left text-sm md:text-base"></i>
        </button>
        <input type="number" id="jumpToPage" min="1" value="1" 
               class="w-12 md:w-16 mx-2 px-1 md:px-2 py-1 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded text-center text-sm">
        <button id="goToPage" class="bg-primary hover:bg-secondary text-white px-2 md:px-3 py-1 rounded text-sm md:text-base">
            برو
        </button>
    </div>

    <script>
        // Document content - will be loaded from API
        let documentContent = [];
        let currentPage = 1;
        const bookId = {{ $book->id }};
        const userId = {{ auth()->id() }};
        let lastReadPage = {{ $initialPage }};
        let pointsAwarded = 0;

        // Initialize the document
        async function initDocument() {
            try {
                const response = await fetch(`/api/book/${bookId}/pages`);
                
                // بررسی وضعیت پاسخ
                if (!response.ok) {
                    throw new Error(`خطای HTTP: ${response.status}`);
                }
                
                const data = await response.json();
                
                // بررسی ساختار داده دریافتی
                if (!data.success || !Array.isArray(data.data)) {
                    throw new Error('ساختار داده دریافتی نامعتبر است');
                }
                
                documentContent = data.data.map(page => ({
                    title: page.page_name || `صفحه ${page.page_number || page.id}`,
                    content: page.text || ''
                }));
                
                renderDocument();
                goToPage(lastReadPage);
                updateProgressBar();
                document.getElementById('totalPages').textContent = documentContent.length;
            } catch (error) {
                console.error('Error loading book:', error);
                document.getElementById('documentViewer').innerHTML = `
                    <div class="text-center py-12">
                        <i class="fas fa-exclamation-triangle text-red-500 text-4xl"></i>
                        <p class="mt-4 text-red-500">خطا در بارگذاری کتاب</p>
                        <p class="text-gray-600 mt-2">${error.message}</p>
                        <button onclick="location.reload()" class="mt-4 bg-primary hover:bg-secondary text-white px-4 py-2 rounded">
                            تلاش مجدد
                        </button>
                    </div>
                `;
            }
        }

        // Render pages
        function renderDocument() {
            const container = document.getElementById('documentViewer');
            container.innerHTML = '';
            
            documentContent.forEach((page, index) => {
                const pageElement = document.createElement('div');
                pageElement.className = 'page bg-paper dark:bg-darkPaper mb-8 p-12 text-gray-800 dark:text-gray-200';
                pageElement.dataset.page = index + 1;
                
                const header = document.createElement('div');
                header.className = 'border-b border-gray-300 dark:border-gray-600 pb-2 mb-6 flex justify-between items-center';
                
                const pageNumber = document.createElement('div');
                pageNumber.className = 'text-sm text-gray-500 dark:text-gray-400';
                pageNumber.textContent = `صفحه ${index + 1}`;
                
                const title = document.createElement('h2');
                title.className = 'text-xl font-bold text-primary';
                title.textContent = page.title;
                
                header.appendChild(title);
                header.appendChild(pageNumber);
                
                const content = document.createElement('div');
                content.className = 'leading-relaxed text-justify page-content';
                content.textContent = page.content;
                content.dataset.original = page.content;
                
                pageElement.appendChild(header);
                pageElement.appendChild(content);
                
                container.appendChild(pageElement);
            });
            
            applySettings();
        }

        // Page navigation
        function goToPage(page) {
            if (page < 1) page = 1;
            if (page > documentContent.length) page = documentContent.length;
            
            currentPage = page;
            document.getElementById('currentPage').textContent = page;
            document.getElementById('jumpToPage').value = page;
            
            const pageElement = document.querySelector(`.page[data-page="${page}"]`);
            if (pageElement) {
                pageElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
            
            updateProgressBar();
            updateReadingProgress();
        }
        
        function updateProgressBar() {
            const progress = (currentPage / documentContent.length) * 100;
            document.getElementById('progressBar').style.width = `${progress}%`;
        }

        // Update reading progress and award points
        async function updateReadingProgress() {
            // Only update every 5 pages
            if (currentPage % 5 === 0 && currentPage > lastReadPage) {
                const pagesRead = currentPage - lastReadPage;
                const points = Math.floor(pagesRead / 5);
                
                if (points > 0) {
                    pointsAwarded += points;
                    lastReadPage = currentPage;
                    
                    try {
                        // Send progress to server
                        const response = await fetch('/api/reading-progress', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                book_id: bookId,
                                user_id: userId,
                                page: currentPage,
                                points: points
                            })
                        });
                        
                        const result = await response.json();
                        
                        if (result.success) {
                            // Show points notification
                            showPointsNotification(points);
                        }
                    } catch (error) {
                        console.error('Error updating reading progress:', error);
                    }
                }
            }
        }
        
        function showPointsNotification(points) {
            const notification = document.createElement('div');
            notification.className = 'fixed bottom-20 left-1/2 transform -translate-x-1/2 bg-green-500 text-white px-4 py-2 rounded-full shadow-lg animate-bounce';
            notification.innerHTML = `
                <i class="fas fa-star mr-2"></i>
                شما ${points} امتیاز دریافت کردید!
            `;
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }

        // Search functionality
        let searchTerm = '';
        let searchMatches = [];
        let currentMatchIndex = -1;
        
        function performSearch() {
            searchTerm = document.getElementById('searchInput').value.trim();
            if (!searchTerm) return;
            
            // Reset previous highlights
            document.querySelectorAll('.search-match, .current-match').forEach(el => {
                el.outerHTML = el.innerHTML;
            });
            
            searchMatches = [];
            currentMatchIndex = -1;
            
            // Search through all pages
            const regex = new RegExp(searchTerm, 'gi');
            document.querySelectorAll('.page').forEach(page => {
                const contentDiv = page.querySelector('.page-content');
                const originalContent = contentDiv.dataset.original;
                let content = originalContent;
                let match;
                
                while ((match = regex.exec(originalContent)) !== null) {
                    searchMatches.push({
                        page: parseInt(page.dataset.page),
                        index: match.index,
                        length: match[0].length
                    });
                }
                
                // Highlight matches
                content = content.replace(regex, match => `<span class="search-match">${match}</span>`);
                contentDiv.innerHTML = content;
            });
            
            // Update search results info
            const resultsInfo = document.getElementById('searchResults');
            if (searchMatches.length > 0) {
                resultsInfo.textContent = `تعداد نتایج: ${searchMatches.length} مورد`;
                highlightCurrentMatch(0);
            } else {
                resultsInfo.textContent = 'هیچ نتیجه‌ای یافت نشد.';
            }
        }
        
        function highlightCurrentMatch(index) {
            if (searchMatches.length === 0) return;
            
            // Remove previous current match highlight
            document.querySelectorAll('.current-match').forEach(el => {
                el.className = 'search-match';
            });
            
            currentMatchIndex = index;
            const match = searchMatches[currentMatchIndex];
            
            // Go to page with the match
            goToPage(match.page);
            
            // Highlight the current match
            const pageElement = document.querySelector(`.page[data-page="${match.page}"]`);
            const contentDiv = pageElement.querySelector('.page-content');
            
            // Create a temporary element to work with
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = contentDiv.innerHTML;
            
            // Find all search matches in this page
            const matches = tempDiv.querySelectorAll('.search-match');
            
            // Find the correct match based on index
            let charCount = 0;
            let foundIndex = -1;
            
            for (let i = 0; i < matches.length; i++) {
                const text = matches[i].textContent;
                if (charCount <= match.index && charCount + text.length >= match.index) {
                    foundIndex = i;
                    break;
                }
                charCount += text.length;
            }
            
            if (foundIndex !== -1) {
                matches[foundIndex].className = 'current-match';
                contentDiv.innerHTML = tempDiv.innerHTML;
                
                // Scroll to the match
                const currentMatchElement = contentDiv.querySelector('.current-match');
                currentMatchElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            
            // Update results info
            document.getElementById('searchResults').textContent = 
                `نتیجه ${currentMatchIndex + 1} از ${searchMatches.length}`;
        }

        // Settings management
        function applySettings() {
            // Get settings from localStorage or use defaults
            const settings = {
                theme: localStorage.getItem('pdfTheme') || 'light',
                fontSize: localStorage.getItem('pdfFontSize') || 16,
                fontFamily: localStorage.getItem('pdfFontFamily') || 'vazirmatn',
                lineSpacing: localStorage.getItem('pdfLineSpacing') || 1.5
            };
            
            // Apply theme
            document.documentElement.className = '';
            document.documentElement.classList.add(settings.theme);
            
            // Apply font size
            document.documentElement.style.fontSize = `${settings.fontSize}px`;
            document.getElementById('fontSizeDisplay').textContent = `${settings.fontSize}px`;
            
            // Apply font family
            document.documentElement.style.fontFamily = getFontFamily(settings.fontFamily);
            
            // Apply line spacing
            document.querySelectorAll('.page-content').forEach(el => {
                el.style.lineHeight = settings.lineSpacing;
            });
            
            // Update UI to reflect current settings
            document.querySelectorAll('.theme-option').forEach(option => {
                option.classList.remove('active');
                if (option.dataset.theme === settings.theme) {
                    option.classList.add('active');
                }
            });
            
            document.querySelectorAll('.font-option').forEach(option => {
                option.classList.remove('border-primary', 'border-2');
                if (option.dataset.font === settings.fontFamily) {
                    option.classList.add('border-primary', 'border-2');
                }
            });
            
            document.getElementById('lineSpacing').value = settings.lineSpacing;
        }
        
        function getFontFamily(font) {
            switch(font) {
                case 'vazirmatn': return 'Vazirmatn, sans-serif';
                case 'tahoma': return 'Tahoma, sans-serif';
                case 'arial': return 'Arial, sans-serif';
                case 'times': return 'Times New Roman, serif';
                default: return 'Vazirmatn, sans-serif';
            }
        }

        // Event listeners
        document.addEventListener('DOMContentLoaded', () => {
            initDocument();
            
            // Page navigation
            document.getElementById('prevPage').addEventListener('click', () => goToPage(currentPage - 1));
            document.getElementById('nextPage').addEventListener('click', () => goToPage(currentPage + 1));
            document.getElementById('goToPage').addEventListener('click', () => {
                const page = parseInt(document.getElementById('jumpToPage').value);
                goToPage(page);
            });
            
            // Search functionality
            document.getElementById('searchBtn').addEventListener('click', () => {
                document.getElementById('searchPanel').classList.toggle('hidden');
                document.getElementById('searchInput').focus();
            });
            
            document.getElementById('closeSearch').addEventListener('click', () => {
                document.getElementById('searchPanel').classList.add('hidden');
            });
            
            document.getElementById('searchInput').addEventListener('keyup', e => {
                if (e.key === 'Enter') {
                    performSearch();
                }
            });
            
            document.getElementById('searchNext').addEventListener('click', () => {
                if (searchMatches.length > 0) {
                    const nextIndex = (currentMatchIndex + 1) % searchMatches.length;
                    highlightCurrentMatch(nextIndex);
                }
            });
            
            document.getElementById('searchPrev').addEventListener('click', () => {
                if (searchMatches.length > 0) {
                    const prevIndex = (currentMatchIndex - 1 + searchMatches.length) % searchMatches.length;
                    highlightCurrentMatch(prevIndex);
                }
            });
            
            // Settings functionality
            document.getElementById('settingsBtn').addEventListener('click', () => {
                document.getElementById('settingsPanel').classList.remove('translate-x-full');
            });
            
            document.getElementById('closeSettings').addEventListener('click', () => {
                document.getElementById('settingsPanel').classList.add('translate-x-full');
            });
            
            // Theme selection
            document.querySelectorAll('.theme-option').forEach(option => {
                option.addEventListener('click', () => {
                    const theme = option.dataset.theme;
                    localStorage.setItem('pdfTheme', theme);
                    applySettings();
                });
            });
            
            // Font size
            document.getElementById('increaseFont').addEventListener('click', () => {
                const currentSize = parseInt(localStorage.getItem('pdfFontSize') || 16);
                const newSize = Math.min(currentSize + 1, 24);
                localStorage.setItem('pdfFontSize', newSize);
                applySettings();
            });
            
            document.getElementById('decreaseFont').addEventListener('click', () => {
                const currentSize = parseInt(localStorage.getItem('pdfFontSize') || 16);
                const newSize = Math.max(currentSize - 1, 12);
                localStorage.setItem('pdfFontSize', newSize);
                applySettings();
            });
            
            // Font family
            document.querySelectorAll('.font-option').forEach(option => {
                option.addEventListener('click', () => {
                    const font = option.dataset.font;
                    localStorage.setItem('pdfFontFamily', font);
                    applySettings();
                });
            });
            
            // Line spacing
            document.getElementById('lineSpacing').addEventListener('input', e => {
                const spacing = parseFloat(e.target.value);
                localStorage.setItem('pdfLineSpacing', spacing);
                applySettings();
            });
            
            // Fullscreen mode
            document.getElementById('fullscreenBtn').addEventListener('click', () => {
                if (!document.fullscreenElement) {
                    document.documentElement.requestFullscreen().catch(err => {
                        console.error(`Error attempting to enable fullscreen: ${err.message}`);
                    });
                } else {
                    document.exitFullscreen();
                }
            });
            
            // Scroll to update current page
            window.addEventListener('scroll', () => {
                const pages = document.querySelectorAll('.page');
                let current = currentPage;
                
                pages.forEach(page => {
                    const rect = page.getBoundingClientRect();
                    if (rect.top <= 100 && rect.bottom >= 100) {
                        current = parseInt(page.dataset.page);
                    }
                });
                
                if (current !== currentPage) {
                    currentPage = current;
                    document.getElementById('currentPage').textContent = currentPage;
                    document.getElementById('jumpToPage').value = currentPage;
                    updateProgressBar();
                    updateReadingProgress();
                }
            });
        });
    </script>
</body>
</html>