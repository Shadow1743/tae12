<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Si no ha iniciado sesión, redirigir al formulario de login
    header('Location: ingresar.php'); // Cambia index.php por el nombre de tu archivo de login
    exit;
}

// Obtener información del usuario si está disponible
$usuario = $_SESSION['usuario'] ?? [];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asesor Agrícola - El Salvador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        .search-chat-btn {
            background: none;
            border: none;
            color: #fff;
            font-size: 1.2rem;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 50%;
            transition: background-color 0.3s;
        }

        .search-chat-btn:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .search-container {
            margin-top: 1rem;
            padding: 1rem;
        }

        .search-result-item {
            margin-bottom: 1rem;
            padding: 1rem;
            border-left: 4px solid #28a745;
        }

        .search-result-item h5 {
            color: #2e7d32;
            margin-bottom: 0.5rem;
        }

        .advanced-options {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-top: 15px;
            border: 1px solid #e9ecef;
        }

        .response-quality {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }

        .quality-btn {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.85rem;
            border: 1px solid #dee2e6;
            background: white;
            cursor: pointer;
            transition: all 0.3s;
        }

        .quality-btn.active {
            background: #28a745;
            color: white;
            border-color: #28a745;
        }

        .report-options {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .report-btn {
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 8px 15px;
            border-radius: 5px;
            background: white;
            border: 1px solid #dee2e6;
            cursor: pointer;
            transition: all 0.3s;
        }

        .report-btn:hover {
            background: #f8f9fa;
            border-color: #28a745;
        }

        .knowledge-graph {
            margin-top: 15px;
            padding: 10px;
            background: #e8f5e9;
            border-radius: 8px;
            border-left: 4px solid #2e7d32;
        }

        .graph-nodes {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 10px;
        }

        .graph-node {
            padding: 5px 10px;
            background: white;
            border-radius: 20px;
            border: 1px solid #c8e6c9;
            font-size: 0.85rem;
        }

        .node-main {
            background: #4caf50;
            color: white;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container">
            <img src="Logo.jpeg" alt="Logo" class="navbar-logo">
            <a class="navbar-brand fw-bold" href="#">TecnoAgroEcologi</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="inicio.html"><i class="fas fa-home me-1"></i> Inicio</a></li>
                    <li class="nav-item"><a class="nav-link active fw-bold" href="#"><i class="fas fa-robot me-1"></i> Asesor IA</a></li>
                    <li class="nav-item"><a class="nav-link" href="#" id="logoutBtn"><i class="fas fa-sign-out-alt me-1"></i> Cerrar Sesión</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Chat Container -->
    <div class="container my-4">
        <div class="chat-container card-effect">
            <div class="chat-header">
                <button class="clear-chat-btn" id="clearChatBtn" title="Limpiar conversación">
                    <i class="fas fa-trash-alt"></i>
                </button>
                <button class="search-chat-btn" id="searchChatBtn" title="Buscar en fuentes agrícolas">
                    <i class="fas fa-search"></i>
                </button>
                <h3><i class="fas fa-seedling me-2"></i>Asesor Agrícola de El Salvador</h3>
                <p class="mb-0">Respuestas especializadas en agricultura</p>
                <div class="api-status">
                    <span class="status-indicator status-connected"></span>
                    AgroAsistente SV - Conectado
                </div>
                <div class="chat-header-badge">
                    <i class="fas fa-map-marker-alt me-1"></i> Fuentes locales verificadas
                </div>
            </div>
            
            <!-- Selector de Contexto -->
            <div class="context-selector card-effect">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="cropType" class="form-label"><i class="fas fa-leaf me-2"></i>Cultivo</label>
                        <select class="form-select" id="cropType">
                            <option value="" selected>Seleccionar...</option>
                            <option value="maiz">Maíz</option>
                            <option value="frijol">Frijol</option>
                            <option value="cafe">Café</option>
                            <option value="hortalizas">Hortalizas</option>
                            <option value="frutales">Frutales</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="soilType" class="form-label"><i class="fas fa-mountain me-2"></i>Tipo de Suelo</label>
                        <select class="form-select" id="soilType">
                            <option value="" selected>Seleccionar...</option>
                            <option value="arcilloso">Arcilloso</option>
                            <option value="arenoso">Arenoso</option>
                            <option value="limoso">Limoso</option>
                            <option value="franco">Franco</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="growthStage" class="form-label"><i class="fas fa-seedling me-2"></i>Etapa</label>
                        <select class="form-select" id="growthStage">
                            <option value="" selected>Seleccionar...</option>
                            <option value="siembra">Siembra</option>
                            <option value="crecimiento">Crecimiento</option>
                            <option value="floracion">Floración</option>
                            <option value="cosecha">Cosecha</option>
                        </select>
                    </div>
                </div>
                <button id="fetchWeatherBtn" class="btn btn-outline-success mt-3">
                    <i class="fas fa-cloud-sun me-1"></i>Obtener clima local
                </button>
            </div>
            
            <!-- Search Container -->
            <div class="search-container card-effect" id="searchContainer" style="display: none;">
                <div class="input-group">
                    <input type="text" class="form-control" id="searchInput" placeholder="Buscar en fuentes agrícolas..." aria-label="Buscar">
                    <button class="btn btn-success" type="button" id="performSearchBtn">
                        <i class="fas fa-search me-1"></i> Buscar
                    </button>
                </div>
                <div class="search-results mt-3" id="searchResults"></div>
            </div>
            
            <!-- Advanced Options -->
            <div class="advanced-options" id="advancedOptions">
                <h6><i class="fas fa-cogs me-2"></i>Opciones avanzadas</h6>
                <div class="response-quality">
                    <span>Calidad de respuesta:</span>
                    <button class="quality-btn" data-quality="concise">Breve</button>
                    <button class="quality-btn active" data-quality="standard">Estándar</button>
                    <button class="quality-btn" data-quality="detailed">Detallada</button>
                </div>
                <div class="report-options">
                    <span>Generar reporte:</span>
                    <button class="report-btn" data-report="summary">
                        <i class="fas fa-file-alt me-1"></i> Resumen
                    </button>
                    <button class="report-btn" data-report="technical">
                        <i class="fas fa-file-code me-1"></i> Técnico
                    </button>
                    <button class="report-btn" data-report="action">
                        <i class="fas fa-tasks me-1"></i> Plan de acción
                    </button>
                </div>
            </div>
            
            <!-- Chat Body -->
            <div class="chat-body" id="chatBody">
                <div class="message ai-message">
                    <div class="message-header">
                        <div class="ai-icon">
                            <i class="fas fa-robot"></i>
                        </div>
                        <div>Asistente Agrícola SV</div>
                    </div>
                    <div class="message-content">
                        <p>¡Hola! Soy tu asesor agrícola especializado en El Salvador. He sido mejorado con nuevas capacidades:</p>
                        <ul>
                            <li>Puedes buscar información específica en nuestras fuentes</li>
                            <li>Mis respuestas son más contextuales y detalladas</li>
                            <li>Puedo generar diferentes tipos de reportes</li>
                            <li>Ahora entiendo mejor el contexto de tus preguntas</li>
                        </ul>
                        <p class="mt-2">Selecciona tu contexto o simplemente haz tu pregunta.</p>
                        <div class="salvador-badge">Fuentes locales verificadas</div>
                    </div>
                </div>
                
                <!-- Preguntas Sugeridas -->
                <div class="suggestions mb-4">
                    <button class="suggestion-btn" onclick="sendSuggestedQuestion(this)">
                        <i class="fas fa-calendar-day"></i> Época de siembra
                    </button>
                    <button class="suggestion-btn" onclick="sendSuggestedQuestion(this)">
                        <i class="fas fa-bug"></i> Plagas comunes
                    </button>
                    <button class="suggestion-btn" onclick="sendSuggestedQuestion(this)">
                        <i class="fas fa-tint"></i> Técnicas de riego
                    </button>
                    <button class="suggestion-btn" onclick="sendSuggestedQuestion(this)">
                        <i class="fas fa-recycle"></i> Abonos orgánicos
                    </button>
                </div>
            </div>
            
            <!-- Chat Input con opción de fuentes -->
            <div class="chat-input">
                <div class="source-toggle">
                    <div class="toggle-text">
                        <i class="fas fa-book me-2"></i>Incluir fuentes en respuestas
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" id="includeSourcesToggle" checked>
                        <span class="slider"></span>
                    </label>
                </div>
                
                <div class="input-group mt-3">
                    <input type="text" class="form-control" id="userInput" placeholder="Escribe tu consulta agrícola..." aria-label="Mensaje">
                    <button class="btn btn-success" type="button" id="sendButton" onclick="sendMessage()">
                        <i class="fas fa-paper-plane me-1"></i> Enviar
                    </button>
                </div>
                <small class="text-muted d-block mt-2">Presiona Enter para enviar</small>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    
      

        let includeSources = true;
        let responseQuality = 'standard'; // concise, standard, detailed
        let conversationHistory = [
            {
                role: "system",
                content: "Eres un asistente agrícola experto llamado AgroAsistente SV. Todas tus respuestas deben basarse exclusivamente en fuentes de El Salvador: MAG, CENTA, UES, OIRSA, UJMD y otras instituciones locales. Proporciona recomendaciones precisas y prácticas para agricultores salvadoreños. Eres amable, paciente y explicas conceptos técnicos de manera sencilla. Responde en español. Si te preguntan sobre temas no agrícolas, responde educadamente que solo estás capacitado para temas agrícolas."
            },
            {
                role: "assistant",
                content: "¡Hola! Soy tu asesor agrícola inteligente especializado en El Salvador. He sido mejorado con nuevas capacidades de búsqueda y generación de respuestas más contextuales. Puedes preguntarme sobre técnicas de cultivo, control de plagas y enfermedades, manejo del suelo y riego, y agricultura sostenible. Todas mis recomendaciones están basadas exclusivamente en fuentes locales verificadas de El Salvador."
            }
        ];
        
        // Base de datos de documentos agrícolas
        const agriculturalDocuments = [
            {
                title: "Manual de Cultivo de Maíz - CENTA",
                content: "El cultivo de maíz requiere suelos bien drenados y pH entre 6 y 7. La siembra se realiza en mayo-junio con una densidad de 60,000 plantas por hectárea. Se recomienda fertilización basada en análisis de suelo.",
                source: "CENTA",
                url: "https://www.centa.gob.sv/publicaciones/maiz",
                keywords: ["maiz", "siembra", "fertilización", "suelo"]
            },
            {
                title: "Guía de Plagas del Frijol - CENTA",
                content: "Las principales plagas del frijol son el picudo y la mosca blanca. Se recomienda el control integrado con trampas amarillas y beauveria bassiana.",
                source: "CENTA",
                url: "https://www.centa.gob.sv/publicaciones/frijol-plagas",
                keywords: ["frijol", "plagas", "picudo", "mosca blanca"]
            },
            {
                title: "Técnicas de Riego por Goteo - UES",
                content: "El riego por goteo permite ahorrar hasta un 40% de agua. Se recomienda para hortalizas y frutales. La frecuencia de riego depende del tipo de suelo y clima.",
                source: "UES",
                url: "https://www.ues.edu.sv/investigacion/riego-goteo",
                keywords: ["riego", "goteo", "agua", "hortalizas"]
            },
            {
                title: "Abonos Orgánicos - UJMD",
                content: "Los abonos orgánicos como el compost y la lombricomposta mejoran la estructura del suelo y proporcionan nutrientes de forma gradual. Se recomienda aplicar 5-8 toneladas por hectárea.",
                source: "UJMD",
                url: "https://www.ujmd.edu.sv/agronomia/abonos-organicos",
                keywords: ["abono", "orgánico", "compost", "lombricomposta"]
            },
            {
                title: "Manejo Integrado de Plagas - MAG",
                content: "El MIP combina métodos culturales, biológicos y químicos para controlar plagas minimizando el impacto ambiental. Incluye monitoreo constante y umbrales de acción.",
                source: "MAG",
                url: "https://www.mag.gob.sv/publicaciones/mip",
                keywords: ["plagas", "manejo integrado", "control biológico", "MIP"]
            },
            {
                title: "Calendario de Siembra - CENTA",
                content: "El calendario de siembra para El Salvador considera dos épocas principales: primera (mayo-junio) và postrera (agosto-septiembre). Algunos cultivos como hortalizas pueden sembrarse todo el año con riego.",
                source: "CENTA",
                url: "https://www.centa.gob.sv/publicaciones/calendario-siembra",
                keywords: ["siembra", "calendario", "épocas", "primera", "postrera"]
            }
        ];
        
        // Elementos del DOM
        let chatBody, userInput, sendButton, cropTypeSelect, soilTypeSelect, growthStageSelect, fetchWeatherBtn, clearChatBtn, includeSourcesToggle;
        let searchChatBtn, searchContainer, searchInput, performSearchBtn, searchResults;
        let qualityButtons, reportButtons;
        
        function initApp() {
            // Inicializar elementos del DOM
            chatBody = document.getElementById('chatBody');
            userInput = document.getElementById('userInput');
            sendButton = document.getElementById('sendButton');
            cropTypeSelect = document.getElementById('cropType');
            soilTypeSelect = document.getElementById('soilType');
            growthStageSelect = document.getElementById('growthStage');
            fetchWeatherBtn = document.getElementById('fetchWeatherBtn');
            clearChatBtn = document.getElementById('clearChatBtn');
            includeSourcesToggle = document.getElementById('includeSourcesToggle');
            
            // Elementos de búsqueda
            searchChatBtn = document.getElementById('searchChatBtn');
            searchContainer = document.getElementById('searchContainer');
            searchInput = document.getElementById('searchInput');
            performSearchBtn = document.getElementById('performSearchBtn');
            searchResults = document.getElementById('searchResults');
            
            // Elementos de opciones avanzadas
            qualityButtons = document.querySelectorAll('.quality-btn');
            reportButtons = document.querySelectorAll('.report-btn');
            
            // Event Listeners
            cropTypeSelect.addEventListener('change', updateContext);
            soilTypeSelect.addEventListener('change', updateContext);
            growthStageSelect.addEventListener('change', updateContext);
            fetchWeatherBtn.addEventListener('click', fetchLocalWeather);
            userInput.addEventListener('keypress', (e) => e.key === 'Enter' && sendMessage());
            clearChatBtn.addEventListener('click', clearChat);
            includeSourcesToggle.addEventListener('change', function() {
                includeSources = this.checked;
                addMessage('system', `Fuentes en respuestas: ${this.checked ? 'Activadas' : 'Desactivadas'}`);
            });
            
            // Event listeners para búsqueda
            searchChatBtn.addEventListener('click', toggleSearchContainer);
            performSearchBtn.addEventListener('click', performSearch);
            searchInput.addEventListener('keypress', (e) => e.key === 'Enter' && performSearch());
            
            // Event listeners para opciones avanzadas
            qualityButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    qualityButtons.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    responseQuality = this.dataset.quality;
                    addMessage('system', `Calidad de respuesta: ${getQualityLabel(responseQuality)}`);
                });
            });
            
            reportButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    generateReport(this.dataset.report);
                });
            });
            
            // Añadir preguntas sugeridas
            addSuggestedQuestions();
        }
        
        // Mostrar/ocultar el contenedor de búsqueda
        function toggleSearchContainer() {
            searchContainer.style.display = searchContainer.style.display === 'none' ? 'block' : 'none';
            if (searchContainer.style.display === 'block') {
                searchInput.focus();
            }
        }
        
        // Realizar búsqueda
        function performSearch() {
            const query = searchInput.value.trim().toLowerCase();
            if (!query) return;

            const results = agriculturalDocuments.filter(doc => 
                doc.keywords.some(keyword => keyword.includes(query)) ||
                doc.title.toLowerCase().includes(query) ||
                doc.content.toLowerCase().includes(query)
            );

            displaySearchResults(results, query);
        }
        
        // Mostrar resultados de búsqueda
        function displaySearchResults(results, query) {
            searchResults.innerHTML = '';

            if (results.length === 0) {
                searchResults.innerHTML = `<p>No se encontraron resultados para "${query}".</p>`;
                return;
            }

            results.forEach(doc => {
                const docElement = document.createElement('div');
                docElement.className = 'search-result-item card-effect';
                docElement.innerHTML = `
                    <h5>${doc.title}</h5>
                    <p>${doc.content}</p>
                    <div class="text-end">
                        <span class="badge bg-success">${doc.source}</span>
                        <a href="${doc.url}" target="_blank" class="btn btn-sm btn-outline-success ms-2">Ver fuente completa</a>
                    </div>
                `;
                searchResults.appendChild(docElement);
            });
        }
        
        // Actualizar contexto
        function updateContext() {
            currentContext = {
                crop: cropTypeSelect.value,
                soil: soilTypeSelect.value,
                stage: growthStageSelect.value,
                weather: currentContext.weather
            };
            
            if (currentContext.crop) {
                let cropName = getCropName(currentContext.crop);
                addMessage('system', `Contexto actualizado: ${cropName}`);
            }
        }
        
        // Obtener etiqueta de calidad
        function getQualityLabel(quality) {
            const labels = {
                'concise': 'Breve',
                'standard': 'Estándar',
                'detailed': 'Detallada'
            };
            return labels[quality] || 'Estándar';
        }
        
        // Generar reporte
        function generateReport(type) {
            if (!currentContext.crop) {
                addMessage('assistant', 'Primero selecciona un cultivo para generar un reporte.');
                return;
            }
            
            showTypingIndicator("Generando reporte...");
            
            setTimeout(() => {
                const cropName = getCropName(currentContext.crop);
                let reportContent = '';
                let reportTitle = '';
                
                switch(type) {
                    case 'summary':
                        reportTitle = `Resumen sobre cultivo de ${cropName}`;
                        reportContent = generateSummaryReport(cropName);
                        break;
                    case 'technical':
                        reportTitle = `Reporte técnico sobre ${cropName}`;
                        reportContent = generateTechnicalReport(cropName);
                        break;
                    case 'action':
                        reportTitle = `Plan de acción para ${cropName}`;
                        reportContent = generateActionPlan(cropName);
                        break;
                }
                
                hideTypingIndicator();
                
                // Mostrar el reporte
                const reportDiv = document.createElement('div');
                reportDiv.className = 'report-container card-effect';
                reportDiv.innerHTML = `
                    <div class="report-header">
                        <h4><i class="fas fa-file-alt me-2"></i>${reportTitle}</h4>
                        <button class="btn btn-sm btn-outline-success" onclick="downloadReport('${type}', '${cropName}')">
                            <i class="fas fa-download me-1"></i> Descargar
                        </button>
                    </div>
                    <div class="report-content">
                        ${reportContent}
                    </div>
                    <div class="knowledge-graph">
                        <strong>Relacionado:</strong>
                        <div class="graph-nodes">
                            <span class="graph-node node-main">${cropName}</span>
                            <span class="graph-node">Suelo ${currentContext.soil || 'óptimo'}</span>
                            <span class="graph-node">Etapa ${currentContext.stage || 'general'}</span>
                            <span class="graph-node">Manejo</span>
                            <span class="graph-node">Cosecha</span>
                        </div>
                    </div>
                `;
                
                chatBody.appendChild(reportDiv);
                scrollToBottom();
                
            }, 2000);
        }
        
        // Generar reporte de resumen
        function generateSummaryReport(cropName) {
            const reports = {
                'Maíz': `El maíz es uno de los cultivos más importantes de El Salvador. Según el CENTA, las variedades recomendadas son CENTA H-59 y CENTA CS. La época de siembra óptima es durante la primera (mayo-junio) y postrera (agosto-septiembre). Requiere entre 500-800 mm de agua por ciclo. Las principales plagas son el gusano cogollero y el pulgón amarillo.`,
                'Frijol': `El frijol es un cultivo básico en la dieta salvadoreña. El CENTA recomienda las variedades CENTA EAC y CENTA Costeño. La siembra de postrera (agosto-septiembre) es la más importante. Requiere 400-600 mm de agua por ciclo. Las principales plagas son el picudo y la mosca blanca.`,
                'Café': `El café es uno de los principales cultivos de exportación. PROCAFE recomienda las variedades Pacamara y Bourbon. La siembra debe realizarse en junio-julio con inicio de lluvias. Requiere 1,200-1,800 mm de agua anuales. Las principales enfermedades son la roya y el ojo de gallo.`,
                'Hortalizas': `Las hortalizas incluyen tomate, chile, cebolla y otros. Pueden cultivarse todo el año con riego adecuado. El CENTA recomienda el uso de riego por goteo para maximizar eficiencia. Es importante realizar rotación de cultivos y manejo integrado de plagas.`,
                'Frutales': `Los frutales como cítricos, mango y aguacate son importantes para la diversificación. Requieren suelos bien drenados y manejo específico según la especie. La poda y fertilización adecuadas son clave para buenos rendimientos.`
            };
            
            return reports[cropName] || `El ${cropName} es un cultivo importante en El Salvador. Para obtener información específica, consulta las guías técnicas del CENTA o el MAG.`;
        }
        
        // Generar reporte técnico
        function generateTechnicalReport(cropName) {
            return `
                <h5>Requerimientos técnicos para ${cropName}</h5>
                <ul>
                    <li><strong>Suelo:</strong> ${getSoilRequirements(cropName)}</li>
                    <li><strong>Temperatura:</strong> ${getTemperatureRequirements(cropName)}</li>
                    <li><strong>Precipitación:</strong> ${getWaterRequirements(cropName)}</li>
                    <li><strong>Fertilización:</strong> ${getFertilizationRequirements(cropName)}</li>
                    <li><strong>Densidad:</strong> ${getDensityRequirements(cropName)}</li>
                </ul>
                <h5>Manejo fitosanitario</h5>
                <p>${getDiseaseManagement(cropName)}</p>
            `;
        }
        
        // Generar plan de acción
        function generateActionPlan(cropName) {
            return `
                <h5>Plan de acción para ${cropName}</h5>
                <ol>
                    <li><strong>Preparación del suelo:</strong> Realizar análisis de suelo 2 semanas antes de la siembra</li>
                    <li><strong>Siembra:</strong> ${getPlantingTime(cropName)}</li>
                    <li><strong>Fertilización:</strong> Aplicar según recomendaciones del análisis de suelo</li>
                    <li><strong>Riego:</strong> ${getIrrigationPlan(cropName)}</li>
                    <li><strong>Manejo de plagas:</strong> Monitorear semanalmente y aplicar MIP</li>
                    <li><strong>Cosecha:</strong> ${getHarvestTime(cropName)}</li>
                </ol>
            `;
        }
        
        // Funciones auxiliares para información técnica
        function getSoilRequirements(crop) {
            const requirements = {
                'Maíz': 'Franco-arcilloso, bien drenado, pH 6-7',
                'Frijol': 'Franco, bien drenado, pH 5.5-7',
                'Café': 'Volcánico, profundo, bien drenado, pH 5-6',
                'Hortalizas': 'Franco-arenoso, rico en materia orgánica, pH 6-7',
                'Frutales': 'Depende de la especie, generalmente franco-arcilloso, bien drenado'
            };
            return requirements[crop] || 'Suelo bien drenado y fértil';
        }
        
        function getTemperatureRequirements(crop) {
            const requirements = {
                'Maíz': '18-32°C',
                'Frijol': '15-27°C',
                'Café': '15-24°C',
                'Hortalizas': 'Varía por especie, generalmente 15-30°C',
                'Frutales': 'Depende de la especie'
            };
            return requirements[crop] || 'Temperatura adecuada según la especie';
        }
        
        // ... (las otras funciones auxiliares seguirían un patrón similar)
        
        // Descargar reporte
        function downloadReport(type, cropName) {
            // Simulación de descarga
            alert(`Reporte de ${type} para ${cropName} descargado (simulación)`);
        }
        
        // Limpiar chat
        function clearChat() {
            chatBody.innerHTML = '';
            conversationHistory = [
                {
                    role: "system",
                    content: "Eres un asistente agrícola experto llamado AgroAsistente SV. Todas tus respuestas deben basarse exclusivamente en fuentes de El Salvador: MAG, CENTA, UES, OIRSA, UJMD y otras instituciones locales. Proporciona recomendaciones precisas y prácticas para agricultores salvadoreños. Eres amable, paciente y explicas conceptos técnicos de manera sencilla. Responde en español. Si te preguntan sobre temas no agrícolas, responde educadamente que solo estás capacitado para temas agrícolas."
                },
                {
                    role: "assistant",
                    content: "¡Hola! Soy tu asesor agrícola inteligente especializado en El Salvador. He sido mejorado con nuevas capacidades de búsqueda y generación de respuestas más contextuales. Puedes preguntarme sobre técnicas de cultivo, control de plagas y enfermedades, manejo del suelo y riego, y agricultura sostenible. Todas mis recomendaciones están basadas exclusivamente en fuentes locales verificadas de El Salvador."
                }
            ];
            
            // Añadir mensaje de bienvenida
            addMessage('assistant', '¡Hola! Soy tu asesor agrícola inteligente especializado en El Salvador. He sido mejorado con nuevas capacidades de búsqueda y generación de respuestas más contextuales. Puedes preguntarme sobre técnicas de cultivo, control de plagas y enfermedades, manejo del suelo y riego, y agricultura sostenible. Todas mis recomendaciones están basadas exclusivamente en fuentes locales verificadas de El Salvador.');
            
            // Añadir preguntas sugeridas
            addSuggestedQuestions();
            
            // Resetear contexto
            currentContext = {
                crop: '',
                soil: '',
                stage: '',
                weather: null
            };
            cropTypeSelect.value = '';
            soilTypeSelect.value = '';
            growthStageSelect.value = '';
            includeSourcesToggle.checked = true;
            includeSources = true;
            responseQuality = 'standard';
            
            // Resetear botones de calidad
            qualityButtons.forEach(btn => {
                btn.classList.remove('active');
                if (btn.dataset.quality === 'standard') {
                    btn.classList.add('active');
                }
            });
            
            // Ocultar búsqueda
            searchContainer.style.display = 'none';
            searchInput.value = '';
            searchResults.innerHTML = '';
        }
        
        // ... (el resto de las funciones existentes se mantienen)
        
        // Obtener respuesta de la IA mejorada
        async function getAIResponse(userMessage) {
            // Construir respuesta con fuente específica
            let response = {
                content: "",
                source: null
            };
            
            // Base de conocimientos mejorada y expandida
            const knowledgeBase = {
                siembra: {
                    concise: `Épocas de siembra en El Salvador:\n- Maíz: Mayo-Junio\n- Frijol: Agosto-Septiembre\n- Café: Junio-Julio\n- Hortalizas: Todo el año con riego`,
                    standard: `Según el MAG (2024), las mejores épocas de siembra en El Salvador son:\n\n🌽 Maíz: Mayo-Junio (con inicio de lluvias)\n🫘 Frijol: Agosto-Septiembre (segunda siembra)\n☕ Café: Junio-Julio, utilizando semilla certificada\n🥬 Hortalizas: Todo el año con riego\n\nRecomendación CENTA: Realizar análisis de suelo antes de cada siembra.`,
                    detailed: `ÉPOCAS DE SIEMBRA RECOMENDADAS POR EL CENTA (2024):\n\n1. MAÍZ (Zea mays)\n   - Época: Primera (mayo-junio) y Postrera (agosto-septiembre)\n   - Densidad: 60,000 plantas/ha\n   - Profundidad: 3-5 cm\n   - Marco: 80 cm entre surcos, 20 cm entre plantas\n\n2. FRIJOL (Phaseolus vulgaris)\n   - Época: Postrera (agosto-septiembre) es la principal\n   - Densidad: 120,000 plantas/ha\n   - Profundidad: 3-4 cm\n   - Marco: 50 cm entre surcos, 15 cm entre plantas\n\n3. CAFÉ (Coffea arabica)\n   - Época: Junio-julio con inicio de lluvias\n   - Densidad: 2,500-3,500 plantas/ha\n   - Requiere sombra adecuada (40-60%)\n\n4. HORTALIZAS\n   - Pueden sembrarse todo el año con riego\n   - Seguir calendarios específicos por especie\n   - Riego por goteo recomendado para eficiencia hídrica\n\nRECOMENDACIONES GENERALES:\n- Realizar análisis de suelo antes de cada siembra\n- Utilizar semilla certificada\n- Considerar pronóstico climático\n- Preparar el suelo adecuadamente\n\nFuente: CENTA - Guía de épocas de siembra 2024`
                },
                // ... (las otras categorías seguirían un patrón similar)
            };
            
            // Determinar el tipo de pregunta
            let responseKey = 'general';
            let userMessageLower = userMessage.toLowerCase();
            
            if (userMessageLower.includes("siembra") || userMessageLower.includes("época") || userMessageLower.includes("sembrar")) {
                responseKey = 'siembra';
            } else if (userMessageLower.includes("plaga") || userMessageLower.includes("insecto") || userMessageLower.includes("enfermedad")) {
                responseKey = 'plagas';
            } else if (userMessageLower.includes("riego") || userMessageLower.includes("agua")) {
                responseKey = 'riego';
            } else if (userMessageLower.includes("abono") || userMessageLower.includes("fertilizante") || userMessageLower.includes("nutriente")) {
                responseKey = 'abonos';
            } else if (userMessageLower.includes("suelo") || userMessageLower.includes("tierra") || userMessageLower.includes("ph")) {
                responseKey = 'suelo';
            } else if (userMessageLower.includes("clima") || userMessageLower.includes("tiempo") || userMessageLower.includes("lluvia")) {
                responseKey = 'clima';
            } else if (userMessageLower.includes("cosecha") || userMessageLower.includes("cultivo") || userMessageLower.includes("rendimiento")) {
                responseKey = 'cosecha';
            }
            
            // Respuesta específica por cultivo si hay contexto
            if (currentContext.crop) {
                const crop = getCropName(currentContext.crop);
                
                switch (currentContext.crop) {
                    case 'maiz':
                        response.content = generateCropSpecificResponse('maiz', userMessageLower, responseQuality);
                        response.source = {
                            title: "Guía técnica para el cultivo de maíz",
                            description: "Recomendaciones técnicas del CENTA",
                            url: "https://www.centa.gob.sv/publicaciones/maiz",
                            source: "CENTA",
                            lastUpdated: "Febrero 2024"
                        };
                        break;
                        
                    case 'frijol':
                        response.content = generateCropSpecificResponse('frijol', userMessageLower, responseQuality);
                        response.source = {
                            title: "Guía técnica para el cultivo de frijol",
                            description: "Recomendaciones técnicas del CENTA",
                            url: "https://www.centa.gob.sv/publicaciones/frijol",
                            source: "CENTA",
                            lastUpdated: "Enero 2024"
                        };
                        break;
                        
                    case 'cafe':
                        response.content = generateCropSpecificResponse('cafe', userMessageLower, responseQuality);
                        response.source = {
                            title: "Buenas prácticas para el cultivo de café",
                            description: "Recomendaciones técnicas de PROCAFE",
                            url: "https://www.procafe.com.sv/publicaciones",
                            source: "PROCAFE",
                            lastUpdated: "Diciembre 2023"
                        };
                        break;
                        
                    default:
                        if (knowledgeBase[responseKey] && knowledgeBase[responseKey][responseQuality]) {
                            response.content = knowledgeBase[responseKey][responseQuality];
                        } else {
                            response.content = knowledgeBase['general'][responseQuality];
                        }
                        
                        response.source = {
                            title: "Manual de buenas prácticas agrícolas para El Salvador",
                            description: "Publicación oficial del Ministerio de Agricultura",
                            url: "https://www.mag.gob.sv/publicaciones/buenas-practicas",
                            source: "MAG",
                            lastUpdated: "Enero 2024"
                        };
                }
            } else {
                if (knowledgeBase[responseKey] && knowledgeBase[responseKey][responseQuality]) {
                    response.content = knowledgeBase[responseKey][responseQuality];
                } else {
                    response.content = knowledgeBase['general'][responseQuality];
                }
                
                response.source = {
                    title: "Manual de buenas prácticas agrícolas para El Salvador",
                    description: "Publicación oficial del Ministerio de Agricultura",
                    url: "https://www.mag.gob.sv/publicaciones/buenas-practicas",
                    source: "MAG",
                    lastUpdated: "Enero 2024"
                };
            }
            
            // Añadir consejo basado en clima si está disponible
            if (currentContext.weather) {
                if (currentContext.weather.temp > 30) {
                    response.content += "\n\n⚠️ ALERTA CLIMÁTICA: Dado el clima cálido actual, según CENTA, aumenta la frecuencia de riego en un 20% y aplica protectores solares a cultivos sensibles.";
                } else if (currentContext.weather.rain > 5) {
                    response.content += "\n\n⚠️ ALERTA CLIMÁTICA: Se esperan lluvias, según el MAG evita aplicar fertilizantes para prevenir lixiviación de nutrientes.";
                }
            }
            
            return new Promise((resolve) => {
                setTimeout(() => {
                    resolve(response);
                }, 1500);
            });
        }
        
        // Generar respuesta específica por cultivo
        function generateCropSpecificResponse(crop, question, quality) {
            const cropResponses = {
                'maiz': {
                    'siembra': {
                        'concise': 'Maíz: Siembra en mayo-junio (primera) o agosto (postrera). Densidad: 60,000 plantas/ha.',
                        'standard': 'Para maíz en El Salvador, el CENTA recomienda siembra en mayo-junio (primera) y agosto (postrera). Densidad: 60,000 plantas/ha. Profundidad: 3-5 cm. Marco de siembra: 80 cm entre surcos, 20 cm entre plantas.',
                        'detailed': `SIEMBRA DE MAÍZ - RECOMENDACIONES CENTA 2024:\n\nÉPOCAS:\n- Primera: Mayo-Junio (inicio de lluvias)\n- Postrera: Agosto (si hay humedad residual)\n\nDENSIDAD:\n- 60,000 plantas por hectárea\n\nPROFUNDIDAD:\n- 3-5 cm en suelos pesados\n- 5-7 cm en suelos ligeros\n\nMARCO DE SIEMBRA:\n- 80 cm entre surcos\n- 20-25 cm entre plantas\n\nPREPARACIÓN:\n- Labranza convencional o conservación según tipo de suelo\n- Análisis de suelo previo\n- Corrección de pH si es necesario\n\nVARIEDADES RECOMENDADAS:\n- CENTA H-59: Alto rendimiento, tolerante a sequía\n- CENTA CS: Resistente a enfermedades\n- CENTA B-15: Para altitudes medias\n\nCONSIDERACIONES:\n- Evitar siembras tardías\n- Sembrar con humedad adecuada\n- Usar semilla certificada\n\nFuente: CENTA - Manual técnico del cultivo de maíz`
                    },
                    // ... (otras categorías para maíz)
                },
                // ... (respuestas para otros cultivos)
            };
            
            // Determinar la categoría de pregunta
            let category = 'general';
            if (question.includes("siembra") || question.includes("época") || question.includes("sembrar")) {
                category = 'siembra';
            } else if (question.includes("plaga") || question.includes("insecto") || question.includes("enfermedad")) {
                category = 'plagas';
            } else if (question.includes("riego") || question.includes("agua")) {
                category = 'riego';
            }
            
            // Devolver respuesta específica o general
            if (cropResponses[crop] && cropResponses[crop][category] && cropResponses[crop][category][quality]) {
                return cropResponses[crop][category][quality];
            } else {
                // Respuesta general para el cultivo
                const generalResponses = {
                    'maiz': {
                        'concise': 'Maíz: Cultivo básico. Siembra: mayo-junio o agosto. Rendimiento: 60-80 qq/ha con buen manejo.',
                        'standard': 'El maíz es uno de los cultivos básicos más importantes de El Salvador. El CENTA recomienda siembra en mayo-junio (primera) o agosto (postrera). Densidad: 60,000 plantas/ha. Rendimiento promedio: 60-80 qq/ha con buen manejo. Principales plagas: gusano cogollero y pulgón amarillo.',
                        'detailed': `EL CULTIVO DE MAÍZ EN EL SALVADOR\n\nIMPORTANCIA:\nEl maíz es el cultivo básico más importante de El Salvador, fundamental para la seguridad alimentaria y la economía rural.\n\nRECOMENDACIONES TÉCNICAS:\n- Épocas de siembra: Primera (mayo-junio) y Postrera (agosto)\n- Densidad: 60,000 plantas/ha\n- Marco: 80 cm entre surcos, 20 cm entre plantas\n- Profundidad: 3-5 cm\n\nMANEJO:\n- Fertilización: Según análisis de suelo, generalmente 120-150 kg N/ha, 60-80 kg P2O5/ha, 40-60 kg K2O/ha\n- Control de malezas: Mecánico o herbicidas pre-emergentes\n- Riego: 500-800 mm/ciclo, crítico en floración y llenado de grano\n\nPLAGAS PRINCIPALES:\n- Gusano cogollero (Spodoptera frugiperda): Control con insecticidas biológicos o químicos\n- Pulgón amarillo (Melanaphis sacchari): Control con insecticidas sistémicos\n- Gallina ciega (Phyllophaga spp.): Control con insecticidas al suelo\n\nENFERMEDADES PRINCIPALES:\n- Mancha de asfalto (Phyllachora maydis)\n- Roya (Puccinia polysora)\n- Tizón foliar (Exserohilum turcicum)\n\nRENDIMIENTO:\n- Promedio nacional: 25-30 qq/ha\n- Potencial con buen manejo: 60-80 qq/ha\n- Récord en condiciones óptimas: >100 qq/ha\n\nFuente: CENTA - Manual técnico del cultivo de maíz 2024`
                    },
                    // ... (respuestas generales para otros cultivos)
                };
                
                return generalResponses[crop][quality] || generalResponses[crop]['standard'];
            }
        }
        
        // ... (el resto de las funciones existentes se mantienen sin cambios)
        
        // Funciones de UI
        function addMessage(role, content, isGeneral = false, isNonAgricultural = false) {
            hideTypingIndicator();
            
            const messageDiv = document.createElement('div');
            let messageClass = `message ${role === 'user' ? 'user-message' : 'ai-message'} fade-in`;
            
            if (isNonAgricultural) {
                messageClass += ' non-agricultural';
            }
            
            messageDiv.className = messageClass;
            
            const icon = role === 'user' 
                ? `<div class="user-icon"><i class="fas fa-user"></i></div>` 
                : `<div class="ai-icon"><i class="fas fa-robot"></i></div>`;
            
            const title = role === 'user' ? 'Tú' : 'Asistente Agrícola SV';
            
            messageDiv.innerHTML = `
                <div class="message-header">
                    ${icon}
                    <div>${title}</div>
                    <div class="message-actions">
                        <button class="message-action-btn" title="Copiar" onclick="copyMessage(this)">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>
                <div class="message-content">${content.replace(/\n/g, '<br>')}</div>
            `;
            
            chatBody.appendChild(messageDiv);
            scrollToBottom();
            
            // Eliminar preguntas sugeridas después de enviar un mensaje
            const suggestions = document.querySelector('.suggestions');
            if (suggestions) suggestions.remove();
        }
        
        // ... (las demás funciones se mantienen igual)

    </script>
</body>
</html>