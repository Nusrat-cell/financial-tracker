<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Category Management</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-100">

<div class="max-w-5xl mx-auto mt-10 bg-white shadow rounded p-6">
  <h1 class="text-2xl font-bold mb-6 text-center">📂 Category Management</h1>

  <!-- SUMMARY COUNTS -->
  <div id="summary" class="flex justify-center gap-8 mb-6 text-lg font-semibold text-gray-700">
    <div>Income Categories: <span id="incomeCount">0</span></div>
    <div>Expense Categories: <span id="expenseCount">0</span></div>
    <div>Total: <span id="totalCount">0</span></div>
  </div>

  <!-- SEARCH, FILTER & EXPORT -->
  <div class="flex justify-between mb-6 flex-wrap gap-2">
    <div class="flex gap-2">
      <input id="searchInput" type="text" placeholder="Search category..." class="border rounded p-2 w-64">
      <select id="filterType" class="border rounded p-2">
        <option value="all">All Types</option>
        <option value="income">Income</option>
        <option value="expense">Expense</option>
      </select>
    </div>
    <div class="flex gap-2">
      <button onclick="exportToCSV()" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">⬇️ Export CSV</button>
    </div>
  </div>

  <!-- ADD FORM -->
  <form id="addForm" class="mb-6 grid grid-cols-3 gap-2">
    <input id="categoryName" type="text" placeholder="Category name" class="border rounded p-2" required>
    <select id="type" class="border rounded p-2">
      <option value="income">Income</option>
      <option value="expense">Expense</option>
    </select>
    <button class="bg-blue-500 text-white px-3 py-2 rounded hover:bg-blue-600">Add</button>
  </form>

  <!-- EDIT FORM -->
  <form id="editForm" class="hidden mb-6 grid grid-cols-4 gap-2">
    <input id="editId" type="hidden">
    <input id="editName" type="text" placeholder="Category name" class="border rounded p-2" required>
    <select id="editType" class="border rounded p-2">
      <option value="income">Income</option>
      <option value="expense">Expense</option>
    </select>
    <button class="bg-green-500 text-white px-3 py-2 rounded hover:bg-green-600">Update</button>
    <button type="button" onclick="cancelEdit()" class="bg-gray-400 text-white px-3 py-2 rounded hover:bg-gray-500">Cancel</button>
  </form>

  <!-- TABLE -->
  <table class="w-full border text-center">
    <thead class="bg-gray-50">
      <tr>
        <th class="py-2 cursor-pointer" onclick="sortTable('CategoryId')">ID ▲▼</th>
        <th class="py-2 cursor-pointer" onclick="sortTable('categoryName')">Name ▲▼</th>
        <th class="py-2 cursor-pointer" onclick="sortTable('type')">Type ▲▼</th>
        <th class="py-2">Actions</th>
        <th class="py-2 cursor-pointer" onclick="sortTable('created_at')">Created At ▲▼</th>
      </tr>
    </thead>
    <tbody id="categoryTable"></tbody>
  </table>

  <!-- PAGINATION -->
  <div class="flex justify-center mt-4 space-x-4">
    <button id="prevBtn" class="bg-gray-300 px-4 py-2 rounded hover:bg-gray-400">Previous</button>
    <span id="pageInfo" class="self-center"></span>
    <button id="nextBtn" class="bg-gray-300 px-4 py-2 rounded hover:bg-gray-400">Next</button>
  </div>
</div>

<script>
let allCategories = [];
let currentPage = 1;
const itemsPerPage = 10;
let sortColumn = '';
let sortAsc = true;

// ------------------- Load Categories -------------------
async function loadCategories() {
  try {
    const res = await fetch('../includes/get_user_categories.php');
    const data = await res.json();
    allCategories = data.categories || [];
    currentPage = 1;
    renderTable();
  } catch (err) {
    console.error(err);
  }
}

// ------------------- Render Table -------------------
function getFilteredData() {
  const searchTerm = document.getElementById('searchInput').value.toLowerCase();
  const filterType = document.getElementById('filterType').value;

  return allCategories.filter(cat =>
    cat.categoryName.toLowerCase().includes(searchTerm) &&
    (filterType === 'all' || cat.type === filterType)
  );
}

function renderTable() {
  const table = document.getElementById('categoryTable');
  const filtered = getFilteredData();

  // Update summary
  const incomeCount = allCategories.filter(c => c.type === 'income').length;
  const expenseCount = allCategories.filter(c => c.type === 'expense').length;
  document.getElementById('incomeCount').textContent = incomeCount;
  document.getElementById('expenseCount').textContent = expenseCount;
  document.getElementById('totalCount').textContent = allCategories.length;

  // Pagination
  const totalPages = Math.ceil(filtered.length / itemsPerPage);
  currentPage = Math.max(1, Math.min(currentPage, totalPages));
  const start = (currentPage - 1) * itemsPerPage;
  const paginated = filtered.slice(start, start + itemsPerPage);

  table.innerHTML = '';
  if (paginated.length === 0) {
    table.innerHTML = '<tr><td colspan="5" class="py-3 text-gray-500">No categories found</td></tr>';
  } else {
    paginated.forEach(cat => {
      table.innerHTML += `
        <tr class="border-t">
          <td>${cat.CategoryId}</td>
          <td>${cat.categoryName}</td>
          <td>
            <span class="px-2 py-1 rounded text-white font-semibold ${
              cat.type === 'income' ? 'bg-green-500' : 'bg-red-500'
            }">${cat.type}</span>
          </td>
          <td class="space-x-2">
            <button onclick="editCategory(${cat.CategoryId}, '${cat.categoryName}', '${cat.type}')" class="bg-yellow-500 text-white px-2 py-1 rounded hover:bg-yellow-600">Edit</button>
            <button onclick="deleteCategory(${cat.CategoryId})" class="bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600">Delete</button>
          </td>
          <td>${cat.created_at}</td>
        </tr>`;
    });
  }

  document.getElementById('pageInfo').textContent = `Page ${currentPage} of ${totalPages || 1}`;
  document.getElementById('prevBtn').disabled = currentPage <= 1;
  document.getElementById('nextBtn').disabled = currentPage >= totalPages;
}

// ------------------- Pagination Buttons -------------------
document.getElementById('prevBtn').addEventListener('click', () => { currentPage--; renderTable(); });
document.getElementById('nextBtn').addEventListener('click', () => { currentPage++; renderTable(); });

// ------------------- Add New Category -------------------
document.getElementById('addForm').addEventListener('submit', async e => {
  e.preventDefault();
  const categoryName = document.getElementById('categoryName').value;
  const type = document.getElementById('type').value;

  try {
    const res = await fetch('../includes/add_category.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({ categoryName, type })
    });
    const result = await res.json();

    if (result.error) {
      Swal.fire({ icon: 'error', title: 'Error', text: result.error });
    } else {
      Swal.fire({ icon: 'success', title: 'Added', text: `Category "${categoryName}" added!`, timer: 1500, showConfirmButton: false });
      document.getElementById('addForm').reset();
      loadCategories();
    }
  } catch (err) { console.error(err); }
});

// ------------------- Delete Category -------------------
async function deleteCategory(CategoryId) {
  const confirmed = await Swal.fire({
    icon: 'warning',
    title: 'Delete Category?',
    text: 'This action cannot be undone!',
    showCancelButton: true,
    confirmButtonText: 'Yes, delete it!',
    cancelButtonText: 'Cancel'
  });

  if (confirmed.isConfirmed) {
    await fetch(`../includes/delete_category.php?CategoryId=${CategoryId}`, { method: 'DELETE' });
    Swal.fire({ icon: 'success', title: 'Deleted!', text: 'Category has been deleted.', timer: 1200, showConfirmButton: false });
    loadCategories();
  }
}

// ------------------- Edit Category -------------------
function editCategory(CategoryId, categoryName, type) {
  document.getElementById('editForm').classList.remove('hidden');
  document.getElementById('addForm').classList.add('hidden');
  document.getElementById('editId').value = CategoryId;
  document.getElementById('editName').value = categoryName;
  document.getElementById('editType').value = type;
}

function cancelEdit() {
  document.getElementById('editForm').classList.add('hidden');
  document.getElementById('addForm').classList.remove('hidden');
}

document.getElementById('editForm').addEventListener('submit', async e => {
  e.preventDefault();
  const CategoryId = document.getElementById('editId').value;
  const categoryName = document.getElementById('editName').value;
  const type = document.getElementById('editType').value;

  try {
    const res = await fetch('../includes/update_category.php', {
      method: 'PUT',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({ CategoryId, categoryName, type })
    });
    const result = await res.json();

    if (result.error) {
      Swal.fire({ icon: 'error', title: 'Error', text: result.error });
    } else {
      Swal.fire({ icon: 'success', title: 'Updated', text: `Category "${categoryName}" updated!`, timer: 1500, showConfirmButton: false });
      cancelEdit();
      loadCategories();
    }
  } catch (err) { console.error(err); }
});

// ------------------- Search & Filter -------------------
document.getElementById('searchInput').addEventListener('input', () => { currentPage = 1; renderTable(); });
document.getElementById('filterType').addEventListener('change', () => { currentPage = 1; renderTable(); });

// ------------------- Sorting -------------------
function sortTable(column) {
  if (sortColumn === column) sortAsc = !sortAsc;
  else { sortColumn = column; sortAsc = true; }

  allCategories.sort((a, b) => {
    let valA = a[column], valB = b[column];
    if (typeof valA === 'string') valA = valA.toLowerCase();
    if (typeof valB === 'string') valB = valB.toLowerCase();
    if (valA < valB) return sortAsc ? -1 : 1;
    if (valA > valB) return sortAsc ? 1 : -1;
    return 0;
  });

  renderTable();
}

// ------------------- Export CSV -------------------
function exportToCSV() {
  const filtered = getFilteredData();
  if (filtered.length === 0) { Swal.fire('No categories to export'); return; }

  const header = ['Category ID', 'Name', 'Type', 'Created At'];
  const rows = filtered.map(cat => [cat.CategoryId, cat.categoryName, cat.type, cat.created_at]);
  const csvContent = [header, ...rows].map(e => e.join(',')).join('\n');

  const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
  const url = URL.createObjectURL(blob);
  const link = document.createElement('a');
  link.href = url;
  link.download = 'categories.csv';
  link.click();
}

// ------------------- Initial Load -------------------
loadCategories();
</script>

</body>
</html>
