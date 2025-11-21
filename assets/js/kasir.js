// ==========================================
// KASIR JAVASCRIPT
// ==========================================

// Update current date
function updateCurrentDate() {
  const dateEl = document.getElementById("currentDate");
  if (dateEl) {
    dateEl.textContent = new Date().toLocaleDateString("id-ID", {
      weekday: "long",
      year: "numeric",
      month: "long",
      day: "numeric",
    });
  }
}

// Format number helper
function formatNumber(num) {
  return new Intl.NumberFormat("id-ID").format(num);
}

// Format date helper
function formatDate(dateString) {
  const date = new Date(dateString);
  const options = {
    day: "2-digit",
    month: "short",
    year: "numeric",
    hour: "2-digit",
    minute: "2-digit",
  };
  return date.toLocaleDateString("id-ID", options);
}

// Show detail modal
function showDetail(noTrans, tglTrans) {
  console.log("Show detail:", noTrans, tglTrans);

  // Reset modal
  document.getElementById("detailLoading").style.display = "block";
  document.getElementById("detailContent").style.display = "none";
  document.getElementById("detailError").style.display = "none";

  // Show modal
  const modalEl = document.getElementById("detailModal");
  const modal = new bootstrap.Modal(modalEl);
  modal.show();

  // Fetch detail data
  const url = `kasir_endpoint.php?action=getDetail&notrans=${encodeURIComponent(
    noTrans
  )}&tgl=${encodeURIComponent(tglTrans)}`;
  console.log("Fetching:", url);

  fetch(url)
    .then((response) => {
      console.log("Response status:", response.status);

      const contentType = response.headers.get("content-type");
      if (!contentType || !contentType.includes("application/json")) {
        return response.text().then((text) => {
          console.error("Expected JSON but got:", text.substring(0, 200));
          throw new Error(`Server returned HTML instead of JSON`);
        });
      }
      return response.json();
    })
    .then((data) => {
      console.log("Response data:", data);
      document.getElementById("detailLoading").style.display = "none";

      if (data.success && data.data && data.data.length > 0) {
        const firstItem = data.data[0];

        // Fill header info
        document.getElementById("detailNoTrans").textContent =
          firstItem.nomor || "-";
        document.getElementById("detailNoPB").textContent =
          firstItem.nopb || "-";
        document.getElementById("detailTgl").textContent = firstItem.tgl
          ? formatDate(firstItem.tgl)
          : "-";
        document.getElementById("detailKodeMember").textContent =
          firstItem.kode_member || "-";
        document.getElementById("detailNamaMember").textContent =
          firstItem.nama_member || "-";

        // Fill table
        const tbody = document.getElementById("detailTableBody");
        tbody.innerHTML = "";
        let totalQty = 0;

        data.data.forEach((item, index) => {
          const qty = parseInt(item.qty_order) || 0;
          totalQty += qty;

          const row = document.createElement("tr");
          row.innerHTML = `
                        <td class="fw-semibold">${index + 1}</td>
                        <td class="text-muted">${item.plu || "-"}</td>
                        <td class="fw-semibold">${item.nama || "-"}</td>
                        <td class="text-center">
                            <span class="badge-qty">${formatNumber(qty)}</span>
                        </td>
                    `;
          tbody.appendChild(row);
        });

        document.getElementById("detailTotalQty").textContent =
          formatNumber(totalQty);
        document.getElementById("detailContent").style.display = "block";
      } else {
        document.getElementById("detailError").style.display = "block";
        document.getElementById("detailErrorMessage").textContent =
          data.message || "Data tidak ditemukan untuk transaksi ini.";
      }
    })
    .catch((error) => {
      console.error("Fetch error:", error);
      document.getElementById("detailLoading").style.display = "none";
      document.getElementById("detailError").style.display = "block";
      document.getElementById("detailErrorMessage").textContent =
        "Terjadi kesalahan saat memuat data: " + error.message;
    });
}

// Initialize on page load
document.addEventListener("DOMContentLoaded", function () {
  updateCurrentDate();
});
