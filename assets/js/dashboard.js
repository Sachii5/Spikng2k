// ==========================================
// DASHBOARD JAVASCRIPT
// ==========================================

// Get chart data from PHP (embedded in view)
const chartDataEl = document.getElementById("chartData");
const chartData = chartDataEl ? JSON.parse(chartDataEl.textContent) : {};

const chartLabels = chartData.labels || [];
const currentPeriodValues = chartData.currentMonthValues || [];
const previousPeriodValues = chartData.lastMonthValues || [];

// State variables
let currentView = "daily";
let showComparison = true;
let ordersComparisonChart;

// ==========================================
// DATA AGGREGATION FUNCTIONS
// ==========================================

function aggregateWeekly(data, labels) {
  const weeklyData = [];
  const weeklyLabels = [];
  let weekSum = 0;
  let weekNum = 1;

  for (let i = 0; i < data.length; i++) {
    weekSum += parseInt(data[i]) || 0;
    if ((i + 1) % 7 === 0 || i === data.length - 1) {
      weeklyData.push(weekSum);
      weeklyLabels.push(`Minggu ${weekNum}`);
      weekNum++;
      weekSum = 0;
    }
  }
  return { data: weeklyData, labels: weeklyLabels };
}

function aggregateMonthly(data, labels) {
  const monthlyData = [];
  const monthlyLabels = [];
  const monthMap = {};

  for (let i = 0; i < labels.length; i++) {
    const parts = labels[i].split(" ");
    const monthKey = parts[1] || parts[0];

    if (!monthMap[monthKey]) {
      monthMap[monthKey] = 0;
    }
    monthMap[monthKey] += parseInt(data[i]) || 0;
  }

  const uniqueMonths = [
    ...new Set(
      labels.map((label) => {
        const parts = label.split(" ");
        return parts[1] || parts[0];
      })
    ),
  ];

  uniqueMonths.forEach((month) => {
    if (monthMap[month] !== undefined) {
      monthlyLabels.push(month);
      monthlyData.push(monthMap[month]);
    }
  });

  return { data: monthlyData, labels: monthlyLabels };
}

// ==========================================
// CHART FUNCTIONS
// ==========================================

function initChart() {
  const ctx = document.getElementById("ordersComparisonChart");
  if (!ctx) return;

  const datasets = [
    {
      label: document.getElementById("currentPeriodLabel").textContent,
      data: [...currentPeriodValues],
      backgroundColor: "rgba(54, 162, 235, 0.8)",
      borderColor: "rgba(54, 162, 235, 1)",
      borderWidth: 2,
      borderRadius: 6,
      hoverBackgroundColor: "rgba(54, 162, 235, 1)",
    },
  ];

  if (showComparison) {
    datasets.push({
      label: document.getElementById("previousPeriodLabel").textContent,
      data: [...previousPeriodValues],
      backgroundColor: "rgba(255, 99, 132, 0.8)",
      borderColor: "rgba(255, 99, 132, 1)",
      borderWidth: 2,
      borderRadius: 6,
      hoverBackgroundColor: "rgba(255, 99, 132, 1)",
    });
  }

  ordersComparisonChart = new Chart(ctx.getContext("2d"), {
    type: "bar",
    data: {
      labels: [...chartLabels],
      datasets: datasets,
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false },
        tooltip: {
          mode: "index",
          intersect: false,
          backgroundColor: "rgba(0, 0, 0, 0.8)",
          padding: 12,
          titleFont: { size: 14, weight: "bold" },
          bodyFont: { size: 13 },
          callbacks: {
            label: function (context) {
              return `${context.dataset.label}: ${context.parsed.y} pesanan`;
            },
          },
        },
      },
      scales: {
        x: {
          grid: { display: false },
          title: {
            display: true,
            text: "Tanggal",
            font: { size: 13, weight: "bold" },
          },
        },
        y: {
          beginAtZero: true,
          grid: { color: "rgba(0, 0, 0, 0.05)" },
          title: {
            display: true,
            text: "Jumlah Pesanan",
            font: { size: 13, weight: "bold" },
          },
          ticks: {
            callback: function (value) {
              return value + " pesanan";
            },
          },
        },
      },
    },
  });
}

function changeView(view) {
  currentView = view;

  document
    .querySelectorAll(".btn-view")
    .forEach((btn) => btn.classList.remove("active"));
  document.querySelector(`[data-view="${view}"]`).classList.add("active");

  let newCurrentData, newPreviousData, newLabels;

  if (view === "weekly") {
    const currentWeekly = aggregateWeekly(currentPeriodValues, chartLabels);
    const previousWeekly = aggregateWeekly(previousPeriodValues, chartLabels);
    newCurrentData = currentWeekly.data;
    newPreviousData = previousWeekly.data;
    newLabels = currentWeekly.labels;
    updateChartInfo("mingguan");
  } else if (view === "monthly") {
    const currentMonthly = aggregateMonthly(currentPeriodValues, chartLabels);
    const previousMonthly = aggregateMonthly(previousPeriodValues, chartLabels);
    newCurrentData = currentMonthly.data;
    newPreviousData = previousMonthly.data;
    newLabels = currentMonthly.labels;
    updateChartInfo("bulanan");
  } else {
    newCurrentData = [...currentPeriodValues];
    newPreviousData = [...previousPeriodValues];
    newLabels = [...chartLabels];
    updateChartInfo("harian");
  }

  // Normalize array lengths
  while (newPreviousData.length < newCurrentData.length)
    newPreviousData.push(0);
  while (newCurrentData.length < newPreviousData.length) newCurrentData.push(0);

  ordersComparisonChart.data.labels = newLabels;
  ordersComparisonChart.data.datasets[0].data = newCurrentData;
  if (showComparison && ordersComparisonChart.data.datasets.length > 1) {
    ordersComparisonChart.data.datasets[1].data = newPreviousData;
  }
  ordersComparisonChart.update("active");
}

function toggleComparison() {
  showComparison = document.getElementById("showComparison").checked;

  if (showComparison) {
    if (ordersComparisonChart.data.datasets.length === 1) {
      let comparisonData = [...previousPeriodValues];
      if (currentView === "weekly") {
        comparisonData = aggregateWeekly(
          previousPeriodValues,
          chartLabels
        ).data;
      } else if (currentView === "monthly") {
        comparisonData = aggregateMonthly(
          previousPeriodValues,
          chartLabels
        ).data;
      }

      while (
        comparisonData.length <
        ordersComparisonChart.data.datasets[0].data.length
      ) {
        comparisonData.push(0);
      }

      ordersComparisonChart.data.datasets.push({
        label: document.getElementById("previousPeriodLabel").textContent,
        data: comparisonData,
        backgroundColor: "rgba(255, 99, 132, 0.8)",
        borderColor: "rgba(255, 99, 132, 1)",
        borderWidth: 2,
        borderRadius: 6,
        hoverBackgroundColor: "rgba(255, 99, 132, 1)",
      });
    }
    document.getElementById("previousLegend").style.display = "flex";
  } else {
    if (ordersComparisonChart.data.datasets.length > 1) {
      ordersComparisonChart.data.datasets.pop();
    }
    document.getElementById("previousLegend").style.display = "none";
  }

  ordersComparisonChart.update("active");
}

function updateChartInfo(mode) {
  const infoElement = document.getElementById("chartInfo");
  const modeText =
    mode === "harian"
      ? "per hari"
      : mode === "mingguan"
      ? "per minggu"
      : "per bulan";
  infoElement.innerHTML = `*Data total pesanan ${mode} (jumlah order ${modeText})<br>
        Grafik menunjukkan perbandingan volume pesanan ${modeText} antara periode terpilih dan periode sebelumnya`;
}

// ==========================================
// FILTER FUNCTIONS
// ==========================================

function setQuickFilter(period) {
  const today = new Date();
  let startDate, endDate;

  switch (period) {
    case "today":
      startDate = endDate = formatDate(today);
      break;
    case "yesterday":
      const yesterday = new Date(today);
      yesterday.setDate(yesterday.getDate() - 1);
      startDate = endDate = formatDate(yesterday);
      break;
    case "this_week":
      const thisWeekStart = new Date(today);
      thisWeekStart.setDate(today.getDate() - today.getDay());
      startDate = formatDate(thisWeekStart);
      endDate = formatDate(today);
      break;
    case "last_week":
      const lastWeekEnd = new Date(today);
      lastWeekEnd.setDate(today.getDate() - today.getDay() - 1);
      const lastWeekStart = new Date(lastWeekEnd);
      lastWeekStart.setDate(lastWeekEnd.getDate() - 6);
      startDate = formatDate(lastWeekStart);
      endDate = formatDate(lastWeekEnd);
      break;
    case "this_month":
      startDate = formatDate(
        new Date(today.getFullYear(), today.getMonth(), 1)
      );
      endDate = formatDate(today);
      break;
    case "last_month":
      const lastMonthStart = new Date(
        today.getFullYear(),
        today.getMonth() - 1,
        1
      );
      const lastMonthEnd = new Date(today.getFullYear(), today.getMonth(), 0);
      startDate = formatDate(lastMonthStart);
      endDate = formatDate(lastMonthEnd);
      break;
  }

  document.getElementById("startDate").value = startDate;
  document.getElementById("endDate").value = endDate;
  document.getElementById("filterForm").submit();
}

function formatDate(date) {
  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, "0");
  const day = String(date.getDate()).padStart(2, "0");
  return `${year}-${month}-${day}`;
}

function resetFilter() {
  const today = new Date();
  document.getElementById("startDate").value = formatDate(
    new Date(today.getFullYear(), today.getMonth(), 1)
  );
  document.getElementById("endDate").value = formatDate(today);
  document.getElementById("filterForm").submit();
}

// ==========================================
// INITIALIZATION
// ==========================================

document.addEventListener("DOMContentLoaded", function () {
  initChart();
});
