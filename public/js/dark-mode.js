/**
 * DARK MODE - POSVENTA
 * Maneja el toggle, persistencia y actualización de gráficos Chart.js
 */
(function () {
    'use strict';

    const STORAGE_KEY = 'theme';
    const DARK_CLASS = 'dark-mode';
    const html = document.documentElement;

    let darkModeToggle = null;
    let chartInstances = [];

    /**
     * Detecta si hay preferencia guardada o del sistema
     */
    function getPreferredTheme() {
        const stored = localStorage.getItem(STORAGE_KEY);
        if (stored) {
            return stored === 'dark' ? 'dark' : 'light';
        }
        return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    }

    /**
     * Aplica o remueve el modo oscuro
     */
    function setTheme(theme) {
        const isDark = theme === 'dark';
        html.classList.toggle(DARK_CLASS, isDark);
        localStorage.setItem(STORAGE_KEY, theme);
        updateToggleIcon(isDark);
        updateCharts(isDark);
    }

    /**
     * Alterna entre modos
     */
    function toggleTheme() {
        const isDark = !html.classList.contains(DARK_CLASS);
        setTheme(isDark ? 'dark' : 'light');
    }

    /**
     * Actualiza el ícono del botón toggle
     */
    function updateToggleIcon(isDark) {
        if (!darkModeToggle) return;
        darkModeToggle.innerHTML = isDark
            ? '<i class="fa fa-sun"></i>'
            : '<i class="fa fa-moon"></i>';
        darkModeToggle.setAttribute('aria-label', isDark ? 'Activar modo claro' : 'Activar modo oscuro');
        darkModeToggle.title = isDark ? 'Modo claro' : 'Modo oscuro';
    }

    /**
     * Busca y registra instancias activas de Chart.js
     */
    function findCharts() {
        chartInstances = [];
        if (typeof Chart === 'undefined') return;
        const canvases = document.querySelectorAll('canvas[id$="Chart"], canvas[id$="chart"]');
        canvases.forEach(function (canvas) {
            const chart = Chart.getChart(canvas);
            if (chart) {
                chartInstances.push(chart);
            }
        });
    }

    /**
     * Actualiza colores de gráficos Chart.js según el tema
     */
    function updateCharts(isDark) {
        if (chartInstances.length === 0) return;

        const textColor = isDark ? '#94a3b8' : '#64748b';
        const gridColor = isDark ? 'rgba(255, 255, 255, 0.08)' : 'rgba(0, 0, 0, 0.06)';
        const borderColor = isDark ? '#334155' : '#e2e8f0';

        chartInstances.forEach(function (chart) {
            if (chart.options) {
                // Actualizar escalas
                if (chart.options.scales) {
                    Object.keys(chart.options.scales).forEach(function (key) {
                        const scale = chart.options.scales[key];
                        if (scale.ticks) {
                            if (scale.ticks.color !== undefined) {
                                scale.ticks.color = textColor;
                            }
                        }
                        if (scale.grid) {
                            if (scale.grid.color !== undefined) {
                                scale.grid.color = gridColor;
                            }
                            if (scale.grid.borderColor !== undefined) {
                                scale.grid.borderColor = borderColor;
                            }
                        }
                    });
                }

                // Actualizar plugins
                if (chart.options.plugins) {
                    // Legend
                    if (chart.options.plugins.legend && chart.options.plugins.legend.labels) {
                        if (chart.options.plugins.legend.labels.color !== undefined) {
                            chart.options.plugins.legend.labels.color = textColor;
                        }
                    }

                    // Tooltip
                    if (chart.options.plugins.tooltip) {
                        if (chart.options.plugins.tooltip.backgroundColor !== undefined) {
                            chart.options.plugins.tooltip.backgroundColor = isDark
                                ? '#1e293b'
                                : '#ffffff';
                        }
                        if (chart.options.plugins.tooltip.titleColor !== undefined) {
                            chart.options.plugins.tooltip.titleColor = isDark
                                ? '#f1f5f9'
                                : '#0f172a';
                        }
                        if (chart.options.plugins.tooltip.bodyColor !== undefined) {
                            chart.options.plugins.tooltip.bodyColor = isDark
                                ? '#cbd5e1'
                                : '#334155';
                        }
                        if (chart.options.plugins.tooltip.borderColor !== undefined) {
                            chart.options.plugins.tooltip.borderColor = borderColor;
                        }
                    }
                }

                chart.update('none');
            }
        });
    }

    /**
     * Escucha cambios en la preferencia del sistema
     */
    function listenSystemPreference() {
        const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
        mediaQuery.addEventListener('change', function (e) {
            if (!localStorage.getItem(STORAGE_KEY)) {
                setTheme(e.matches ? 'dark' : 'light');
            }
        });
    }

    /**
     * Inicialización
     */
    function init() {
        darkModeToggle = document.getElementById('darkModeToggle');

        if (!darkModeToggle) {
            // Reintentar si el DOM no ha terminado de cargar
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function () {
                    darkModeToggle = document.getElementById('darkModeToggle');
                    if (darkModeToggle) {
                        darkModeToggle.addEventListener('click', toggleTheme);
                    }
                });
            }
            return;
        }

        // Aplicar tema guardado o preferencia del sistema
        const theme = getPreferredTheme();
        const isDark = theme === 'dark';
        html.classList.toggle(DARK_CLASS, isDark);
        updateToggleIcon(isDark);

        // Registrar evento de toggle
        darkModeToggle.addEventListener('click', toggleTheme);

        // Escuchar cambios en preferencia del sistema
        listenSystemPreference();

        // Buscar gráficos Chart.js existentes
        setTimeout(findCharts, 500);

        // Re-buscar gráficos cuando el DOM cambie (para SPAs o modales)
        const observer = new MutationObserver(function () {
            setTimeout(findCharts, 300);
        });
        observer.observe(document.body, { childList: true, subtree: true });
    }

    // Iniciar cuando el DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
