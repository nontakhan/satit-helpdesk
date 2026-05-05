<?php
require_once 'partials/header.php';
require_once '../db_connect.php';

// --- นับจำนวนรายการในการ์ด (เหมือนเดิม) ---
$sql_pending = "SELECT COUNT(id) as total_pending FROM requests WHERE current_status_id = 1";
$result_pending = $conn->query($sql_pending);
$pending_count = $result_pending->fetch_assoc()['total_pending'];

$sql_processing = "SELECT COUNT(id) as total_processing FROM requests WHERE current_status_id = 2";
$result_processing = $conn->query($sql_processing);
$processing_count = $result_processing->fetch_assoc()['total_processing'];

// นับจำนวนงานที่เสร็จสิ้นในเดือนนี้
$current_month = date('Y-m');
$sql_total_month = "SELECT COUNT(id) as total_month FROM requests WHERE DATE_FORMAT(request_date, '%Y-%m') = '$current_month'";
$result_total_month = $conn->query($sql_total_month);
$total_count_month = $result_total_month->fetch_assoc()['total_month'];

$sql_completed = "SELECT COUNT(id) as total_completed FROM requests WHERE final_status_id = 3 AND DATE_FORMAT(repair_date, '%Y-%m') = '$current_month'";
$result_completed = $conn->query($sql_completed);
$completed_count_month = $result_completed->fetch_assoc()['total_completed'];

?>

<style>
    .dashboard-stat-card {
        border: 0;
        border-radius: 12px;
        box-shadow: 0 12px 28px rgba(18, 63, 115, 0.12);
        overflow: hidden;
        min-height: 142px;
    }

    .dashboard-stat-card .card-body {
        padding: 1.25rem;
    }

    .dashboard-stat-card .stat-icon {
        width: 44px;
        height: 44px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.18);
        font-size: 1.35rem;
    }

    .dashboard-chart-card {
        border: 0;
        border-radius: 12px;
        box-shadow: 0 10px 24px rgba(18, 63, 115, 0.1);
        overflow: hidden;
    }

    .dashboard-chart-card .card-header {
        background: #fff;
        border-bottom: 1px solid rgba(18, 63, 115, 0.08);
        padding: 1rem 1.25rem;
    }
</style>

<div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-4">
    <div>
        <h2 class="mb-1">Dashboard</h2>
        <p class="text-muted mb-0">ภาพรวมรายการแจ้งซ่อมและสถิติการให้บริการ</p>
    </div>
    <a href="report.php" class="btn btn-outline-primary">
        <i class="bi bi-file-earmark-bar-graph me-1"></i> รายงาน
    </a>
</div>

<!-- ส่วนของการ์ดสรุปข้อมูล -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <a href="requests_list.php?status=1" class="text-decoration-none">
            <div class="card dashboard-stat-card text-white" style="background: linear-gradient(135deg, #ff8a00, #ff5a00);">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="opacity-75 mb-1">รอรับเรื่อง</div>
                            <div class="display-6 fw-bold"><?php echo $pending_count; ?></div>
                            <small class="opacity-75">รายการ</small>
                        </div>
                        <span class="stat-icon"><i class="bi bi-hourglass-split"></i></span>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-sm-6 col-xl-3">
        <a href="requests_list.php?status=2" class="text-decoration-none">
            <div class="card dashboard-stat-card text-white" style="background: linear-gradient(135deg, #071b33, #123f73);">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="opacity-75 mb-1">กำลังดำเนินการ</div>
                            <div class="display-6 fw-bold"><?php echo $processing_count; ?></div>
                            <small class="opacity-75">รายการ</small>
                        </div>
                        <span class="stat-icon"><i class="bi bi-tools"></i></span>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-sm-6 col-xl-3">
        <a href="report.php" class="text-decoration-none">
            <div class="card dashboard-stat-card text-white" style="background: linear-gradient(135deg, #1f7a5a, #26a269);">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="opacity-75 mb-1">เสร็จสิ้นเดือนนี้</div>
                            <div class="display-6 fw-bold"><?php echo $completed_count_month; ?></div>
                            <small class="opacity-75">รายการ</small>
                        </div>
                        <span class="stat-icon"><i class="bi bi-check2-circle"></i></span>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-sm-6 col-xl-3">
        <a href="report.php" class="text-decoration-none">
            <div class="card dashboard-stat-card text-white" style="background: linear-gradient(135deg, #1a4f86, #123f73);">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="opacity-75 mb-1">แจ้งซ่อมเดือนนี้</div>
                            <div class="display-6 fw-bold"><?php echo $total_count_month; ?></div>
                            <small class="opacity-75">รายการ</small>
                        </div>
                        <span class="stat-icon"><i class="bi bi-calendar3"></i></span>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>

<!-- ===== ส่วนของกราฟ ===== -->
<div class="row g-4">
    <!-- กราฟแท่ง (จำนวนแจ้งซ่อมรายเดือน) -->
    <div class="col-xl-8">
        <div class="card dashboard-chart-card h-100">
            <div class="card-header">
                <h5 class="card-title mb-0">จำนวนการแจ้งซ่อมรายเดือน (12 เดือนย้อนหลัง)</h5>
            </div>
            <div class="card-body" style="height: 300px;">
                <canvas id="monthlyRequestsChart"></canvas>
            </div>
        </div>
    </div>
    <!-- กราฟวงกลม สัดส่วนงานตามประเภท -->
    <div class="col-xl-4">
        <div class="card dashboard-chart-card h-100">
            <div class="card-header">
                <h5 class="card-title mb-0">สัดส่วนงานตามประเภท</h5>
            </div>
            <div class="card-body" style="height: 300px;">
                <canvas id="categoryRequestsChart"></canvas>
            </div>
        </div>
    </div>
    <!-- กราฟแท่งแบบซ้อน (สถานที่รายเดือน) -->
    <div class="col-12">
        <div class="card dashboard-chart-card">
            <div class="card-header">
                <h5 class="card-title mb-0">สถิติการแจ้งซ่อมตามสถานที่ (6 เดือนย้อนหลัง)</h5>
            </div>
            <div class="card-body" style="height: 350px;">
                <canvas id="locationMonthlyChart"></canvas>
            </div>
        </div>
    </div>
</div>


<!-- เรียกใช้ Chart.js และ Plugins -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
    // Register the datalabels plugin
    Chart.register(ChartDataLabels);

    document.addEventListener('DOMContentLoaded', function () {
        // ดึงข้อมูลจาก API
        axios.get('../api/get_chart_data.php')
            .then(function (response) {
                const chartData = response.data;

                // --- 1. สร้างกราฟแท่ง ---
                new Chart(document.getElementById('monthlyRequestsChart'), {
                    type: 'bar',
                    data: {
                        labels: chartData.monthlyRequests.labels,
                        datasets: [{
                            label: 'จำนวนเรื่อง',
                            data: chartData.monthlyRequests.data,
                            backgroundColor: 'rgba(18, 63, 115, 0.82)',
                            borderColor: 'rgba(18, 63, 115, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: { y: { beginAtZero: true } },
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { datalabels: { display: false } } // ไม่แสดง label บนกราฟแท่ง
                    }
                });

                // --- 2. สร้างกราฟวงกลม (ประเภทงาน) พร้อมแสดง % ---
                new Chart(document.getElementById('categoryRequestsChart'), {
                    type: 'doughnut',
                    data: {
                        labels: chartData.categoryRequests.labels,
                        datasets: [{
                            label: 'จำนวนเรื่อง',
                            data: chartData.categoryRequests.data,
                            backgroundColor: [
                                'rgba(255, 99, 132, 0.7)',
                                'rgba(18, 63, 115, 0.78)',
                                'rgba(255, 206, 86, 0.7)',
                                'rgba(26, 79, 134, 0.78)',
                                'rgba(153, 102, 255, 0.7)',
                                'rgba(255, 159, 64, 0.7)'
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            datalabels: { display: false }, // ไม่แสดง label บนกราฟ แสดงแค่ตอน hover
                            tooltip: {
                                callbacks: {
                                    label: function (context) {
                                        let sum = context.dataset.data.reduce((a, b) => a + b, 0);
                                        let percentage = (context.raw * 100 / sum).toFixed(1) + '%';
                                        return context.label + ': ' + context.raw + ' (' + percentage + ')';
                                    }
                                }
                            }
                        }
                    }
                });

                // --- 3. สร้างกราฟแท่งแบบซ้อน (สถานที่รายเดือน) ---
                new Chart(document.getElementById('locationMonthlyChart'), {
                    type: 'bar',
                    data: chartData.locationMonthly,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: { stacked: true },
                            y: { stacked: true, beginAtZero: true }
                        },
                        plugins: { datalabels: { display: false } } // ไม่แสดง label บนกราฟแท่ง
                    }
                });

            })
            .catch(function (error) {
                console.error("Error fetching chart data:", error);
            });
    });
</script>

<?php
$conn->close();
require_once 'partials/footer.php';
?>
