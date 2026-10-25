<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="theme-color" content="#000000">
    <title>Status Gym - ARIFAH GYM</title>
    <script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.26/webfont.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        * { 
            margin: 0; 
            padding: 0; 
            box-sizing: border-box; 
        }
        
        body { 
            margin: 0; 
            background: #000; 
            display: flex; 
            flex-direction: column;
            align-items: center; 
            justify-content: center; 
            min-height: 100vh; 
            font-family: 'Poppins', sans-serif;
            padding: 20px;
        }
        
        .font-loader { 
            position: absolute; 
            visibility: hidden; 
            height: 0; 
            width: 0; 
            font-family: 'Poppins'; 
        }
        
        #cardContainer {
            max-width: 1050px;
            width: 100%;
            position: relative;
        }
        
        canvas {
            width: 100%;
            height: auto;
            border-radius: 24px;
            box-shadow: 0 20px 80px rgba(9, 146, 194, 0.3);
        }
        
        @media (max-width: 768px) {
            body { 
                padding: 10px; 
            }
        }
    </style>
</head>
<body>
    <div class="font-loader" style="font-weight: 400;">.</div>
    <div class="font-loader" style="font-weight: 900; font-style: italic;">.</div>
    
    <div id="cardContainer">
        <canvas id="statusCard" width="1050" height="550"></canvas>
    </div>

    <!-- Chart Container -->
    <div id="chartContainer" style="max-width: 1050px; width: 100%; margin-top: 30px; padding: 20px 0;">
        <canvas id="busyHoursChart" style="max-height: 300px;"></canvas>
    </div>

    <!-- Day Chart Container -->
    <div id="dayChartContainer" style="max-width: 1050px; width: 100%; margin-top: 30px; padding: 20px 0;">
        <canvas id="busyDaysChart" style="max-height: 300px;"></canvas>
    </div>

    <script>
        // Data gym status dari server
        const gymData = {
            activeMembers: {{ $activeMembers }},
            status: "{{ $status }}",
            statusColor: "{{ $statusColor }}",
            lastUpdate: "{{ now('Asia/Makassar')->format('H:i') }} WITA",
            chartData: @json($chartData),
            dayChartData: @json($dayChartData)
        };

        // Deteksi orientasi dan ukuran layar
        function getCardDimensions() {
            const isMobile = window.innerWidth <= 768;
            const isPortrait = window.innerHeight > window.innerWidth;
            
            if (isMobile && isPortrait) {
                // Mobile Portrait: Kartu vertikal
                return { width: 600, height: 950, orientation: 'portrait' };
            } else {
                // Desktop/Landscape: Kartu horizontal
                return { width: 1050, height: 550, orientation: 'landscape' };
            }
        }

        WebFont.load({
            google: { families: ['Poppins:400,700,900,900i'] },
            active: function() { 
                generateCard();
            },
            inactive: function() { 
                generateCard();
            }
        });

        async function generateCard() {
            const canvas = document.getElementById('statusCard');
            const ctx = canvas.getContext('2d');
            const dimensions = getCardDimensions();
            
            canvas.width = dimensions.width;
            canvas.height = dimensions.height;
            
            await document.fonts.ready;
            
            const W = dimensions.width;
            const H = dimensions.height;
            const R = dimensions.orientation === 'portrait' ? 32 : 48;

            // 1. Background
            ctx.save();
            ctx.beginPath();
            ctx.roundRect(0, 0, W, H, R);
            ctx.clip();
            
            const grad = ctx.createLinearGradient(0, 0, W, H);
            grad.addColorStop(0, '#1e1410');
            grad.addColorStop(0.5, '#0f0905');
            grad.addColorStop(1, '#1e1410');
            ctx.fillStyle = grad;
            ctx.fillRect(0, 0, W, H);

            // 2. Cyan glow top-right
            const flare = ctx.createRadialGradient(W, 0, 0, W, 0, 500);
            flare.addColorStop(0, 'rgba(9,146,194,0.25)');
            flare.addColorStop(1, 'transparent');
            ctx.fillStyle = flare;
            ctx.fillRect(0, 0, W, H);

            // 3. Cyan glow bottom-left
            const flare2 = ctx.createRadialGradient(0, H, 0, 0, H, 350);
            flare2.addColorStop(0, 'rgba(9,146,194,0.10)');
            flare2.addColorStop(1, 'transparent');
            ctx.fillStyle = flare2;
            ctx.fillRect(0, 0, W, H);

            ctx.restore();

            // 5. Card border
            ctx.save();
            ctx.beginPath();
            ctx.roundRect(0, 0, W, H, R);
            ctx.strokeStyle = 'rgba(9,146,194,0.3)';
            ctx.lineWidth = 2;
            ctx.stroke();
            ctx.restore();

            if (dimensions.orientation === 'portrait') {
                renderPortraitLayout(ctx, W, H);
            } else {
                renderLandscapeLayout(ctx, W, H);
            }
        }

        function renderLandscapeLayout(ctx, W, H) {
            const centerX = W / 2;
            const centerY = H / 2;
            let y = 110;

            // Header - Gym name centered
            ctx.fillStyle = "#0992C2";
            ctx.font = "italic 900 52px Poppins";
            ctx.textAlign = "center";
            ctx.fillText("ARIFAH GYM", centerX, y);
            y += 52 + 20;

            ctx.fillStyle = "rgba(255,255,255,0.50)";
            ctx.font = "700 18px Poppins";
            ctx.fillText("MONITORING KERAMAIAN", centerX, y);
            y += 18 + 60;

            // Member count - BIG in center
            ctx.fillStyle = "#ffffff";
            ctx.font = "900 110px Poppins";
            ctx.textBaseline = "middle";
            ctx.fillText(gymData.activeMembers.toString(), centerX, centerY - 10);

            // Label
            ctx.fillStyle = "rgba(255,255,255,0.60)";
            ctx.font = "700 22px Poppins";
            ctx.fillText("MEMBER CHECK-IN", centerX, centerY + 65);

            // Status text - simple without badge
            y = centerY + 110;
            
            ctx.fillStyle = "rgba(255,255,255,0.50)";
            ctx.font = "400 16px Poppins";
            ctx.fillText("Status Gym Saat Ini", centerX, y);
            y += 16 + 15;

            ctx.fillStyle = "#ffffff";
            ctx.font = "900 52px Poppins";
            ctx.fillText(gymData.status, centerX, y);

            // Bottom info
            y = H - 60;
            ctx.fillStyle = "rgba(255,255,255,0.30)";
            ctx.font = "400 13px Poppins";
            ctx.textBaseline = "top";
            ctx.fillText("Terakhir update: " + gymData.lastUpdate, centerX, y);

            ctx.textAlign = "left";
        }

        function renderPortraitLayout(ctx, W, H) {
            const centerX = W / 2;
            const centerY = H / 2;
            let y = 100;

            // Header section
            ctx.fillStyle = "#0992C2";
            ctx.font = "italic 900 46px Poppins";
            ctx.textAlign = "center";
            ctx.fillText("ARIFAH GYM", centerX, y);
            y += 46 + 18;

            ctx.fillStyle = "rgba(255,255,255,0.50)";
            ctx.font = "700 16px Poppins";
            ctx.fillText("MONITORING KERAMAIAN", centerX, y);
            y += 16 + 80;

            // Member count - BIG in center
            ctx.fillStyle = "#ffffff";
            ctx.font = "900 120px Poppins";
            ctx.textBaseline = "middle";
            ctx.fillText(gymData.activeMembers.toString(), centerX, centerY - 30);

            // Label
            ctx.fillStyle = "rgba(255,255,255,0.60)";
            ctx.font = "700 24px Poppins";
            ctx.fillText("MEMBER CHECK-IN", centerX, centerY + 60);

            // Status text - simple without badge
            y = centerY + 120;
            
            ctx.fillStyle = "rgba(255,255,255,0.50)";
            ctx.font = "400 16px Poppins";
            ctx.fillText("Status Gym Saat Ini", centerX, y);
            y += 16 + 18;

            ctx.fillStyle = "#ffffff";
            ctx.font = "900 68px Poppins";
            ctx.textBaseline = "top";
            ctx.fillText(gymData.status, centerX, y);
            
            // Bottom info
            y = H - 80;
            ctx.fillStyle = "rgba(255,255,255,0.30)";
            ctx.font = "400 13px Poppins";
            ctx.textAlign = "center";
            ctx.fillText("Terakhir update: " + gymData.lastUpdate, centerX, y);

            // Reset text align
            ctx.textAlign = "left";
        }

        // Re-render saat orientasi berubah
        window.addEventListener('resize', function() {
            generateCard();
        });

        // Auto refresh setiap 30 detik
        setInterval(function() {
            location.reload();
        }, 30000);

        // Render Chart.js - Jam Teramai
        function renderBusyHoursChart() {
            const data = Object.values(gymData.chartData);
            const labels = [];
            for (let i = 0; i < 24; i++) {
                labels.push(i.toString().padStart(2, '0') + ':00');
            }

            const ctx = document.getElementById('busyHoursChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Jam Teramai',
                        data: data,
                        borderColor: '#0992C2',
                        backgroundColor: 'rgba(9, 146, 194, 0.1)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 3,
                        pointBackgroundColor: '#0992C2',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            display: true,
                            labels: {
                                color: '#fff',
                                font: {
                                    family: 'Poppins',
                                    size: 14,
                                    weight: 'bold'
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0,0,0,0.8)',
                            titleColor: '#0992C2',
                            bodyColor: '#fff',
                            borderColor: '#0992C2',
                            borderWidth: 1,
                            padding: 12,
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    return 'Total: ' + context.parsed.y + ' member';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                                color: 'rgba(255,255,255,0.6)',
                                font: {
                                    family: 'Poppins',
                                    size: 12
                                }
                            },
                            grid: {
                                color: 'rgba(255,255,255,0.1)',
                                drawBorder: false
                            }
                        },
                        x: {
                            ticks: {
                                color: 'rgba(255,255,255,0.6)',
                                font: {
                                    family: 'Poppins',
                                    size: 11
                                },
                                maxRotation: 0,
                                autoSkip: true,
                                maxTicksLimit: 12
                            },
                            grid: {
                                color: 'rgba(255,255,255,0.05)',
                                drawBorder: false
                            }
                        }
                    }
                }
            });
        }

        // Render Chart.js - Hari Teramai
        function renderBusyDaysChart() {
            const dayLabels = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const data = gymData.dayChartData; // Sudah array dengan 7 elemen
            const maxVal = Math.max(...data);
            
            // Warna berdasarkan nilai tertinggi
            const colors = data.map(val => 
                val === maxVal && maxVal > 0 ? '#0992C2' : 'rgba(9, 146, 194, 0.3)'
            );

            const ctx = document.getElementById('busyDaysChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: dayLabels,
                    datasets: [{
                        label: 'Hari Teramai',
                        data: data,
                        backgroundColor: colors,
                        borderColor: '#0992C2',
                        borderWidth: 2,
                        borderRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            display: true,
                            labels: {
                                color: '#fff',
                                font: {
                                    family: 'Poppins',
                                    size: 14,
                                    weight: 'bold'
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0,0,0,0.8)',
                            titleColor: '#0992C2',
                            bodyColor: '#fff',
                            borderColor: '#0992C2',
                            borderWidth: 1,
                            padding: 12,
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    return 'Total: ' + context.parsed.y + ' member';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                                color: 'rgba(255,255,255,0.6)',
                                font: {
                                    family: 'Poppins',
                                    size: 12
                                }
                            },
                            grid: {
                                color: 'rgba(255,255,255,0.1)',
                                drawBorder: false
                            }
                        },
                        x: {
                            ticks: {
                                color: 'rgba(255,255,255,0.6)',
                                font: {
                                    family: 'Poppins',
                                    size: 12
                                }
                            },
                            grid: {
                                color: 'rgba(255,255,255,0.05)',
                                drawBorder: false
                            }
                        }
                    }
                }
            });
        }

        // Render chart setelah canvas status selesai
        setTimeout(() => {
            renderBusyHoursChart();
            renderBusyDaysChart();
        }, 100);
    </script>
</body>
</html>
