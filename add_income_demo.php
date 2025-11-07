<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Income Demo</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

<div class="max-w-lg mx-auto mt-10 bg-white shadow-md rounded-lg p-6">
  <h1 class="text-2xl font-bold mb-6 text-center text-gray-700">💵 Add Income (Demo)</h1>

  <form id="incomeForm" class="space-y-4">
    <div>
      <label class="block text-gray-600 font-medium mb-1">Income Source</label>
      <input id="incomeName" type="text" placeholder="e.g. Salary, Freelance Work"
             class="w-full border rounded p-2" required>
    </div>

    <div>
      <label class="block text-gray-600 font-medium mb-1">Amount</label>
      <input id="amount" type="number" placeholder="e.g. 1000.00"
             class="w-full border rounded p-2" required>
    </div>

    <div>
      <label class="block text-gray-600 font-medium mb-1">Category</label>
      <select id="incomeCategoryDropdown" class="w-full border rounded p-2" required>
        <option value="">-- Loading categories... --</option>
      </select>
    </div>

    <div>
      <button class="w-full bg-green-500 hover:bg-green-600 text-white py-2 rounded">
        Add Income
      </button>
    </div>
  </form>
</div>

<script>
// ✅ Load income categories dynamically from your API
async function loadIncomeCategories() {
  const dropdown = document.getElementById('incomeCategoryDropdown');
  try {
    const res = await fetch('../includes/get_categories_by_type.php?type=income');
    const data = await res.json();

    dropdown.innerHTML = '<option value="">-- Select Category --</option>';
    data.categories.forEach(cat => {
      dropdown.innerHTML += `<option value="${cat.CategoryId}">${cat.categoryName}</option>`;
    });
  } catch (err) {
    console.error(err);
    dropdown.innerHTML = '<option value="">Error loading categories</option>';
  }
}

document.addEventListener('DOMContentLoaded', loadIncomeCategories);

// Demo form submission (for testing only)
document.getElementById('incomeForm').addEventListener('submit', e => {
  e.preventDefault();
  const name = document.getElementById('incomeName').value;
  const amount = document.getElementById('amount').value;
  const categoryId = document.getElementById('incomeCategoryDropdown').value;

  if (!categoryId) {
    alert('Please select a category');
    return;
  }

  alert(`Income added!\n\nSource: ${name}\nAmount: $${amount}\nCategory ID: ${categoryId}`);
  e.target.reset();
});
</script>

</body>
</html>
