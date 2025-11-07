<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Category Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

<div class="max-w-4xl mx-auto mt-10 bg-white shadow-md rounded-lg p-6">
  <h1 class="text-3xl font-bold text-center mb-6 text-gray-700">📊 Category Dashboard</h1>

  <div id="summary" class="grid grid-cols-3 gap-4 text-center mb-6">
    <div class="bg-blue-100 p-4 rounded shadow">
      <h2 class="text-lg font-semibold">Income Categories</h2>
      <p id="incomeCount" class="text-2xl font-bold mt-2">0</p>
    </div>
    <div class="bg-red-100 p-4 rounded shadow">
      <h2 class="text-lg font-semibold">Expense Categories</h2>
      <p id="expenseCount" class="text-2xl font-bold mt-2">0</p>
    </div>
    <div class="bg-gray-100 p-4 rounded shadow">
      <h2 class="text-lg font-semibold">Total Categories</h2>
      <p id="totalCount" class="text-2xl font-bold mt-2">0</p>
    </div>
  </div>

  <div class="flex justify-center gap-6 mt-4">
    <a href="add_income_demo.php" class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded shadow">➕ Add Income</a>
    <a href="add_expense_demo.php" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded shadow">➕ Add Expense</a>
    <a href="categories.php" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded shadow">📂 Manage Categories</a>
  </div>
</div>

<script>
async function loadCategoryCounts() {
  try {
    const res = await fetch('../includes/get_user_categories.php');
    const data = await res.json();
    const categories = data.categories || [];

    const incomeCount = categories.filter(c => c.type === 'income').length;
    const expenseCount = categories.filter(c => c.type === 'expense').length;
    document.getElementById('incomeCount').textContent = incomeCount;
    document.getElementById('expenseCount').textContent = expenseCount;
    document.getElementById('totalCount').textContent = categories.length;
  } catch (err) {
    console.error(err);
  }
}

document.addEventListener('DOMContentLoaded', loadCategoryCounts);
</script>

</body>
</html>
