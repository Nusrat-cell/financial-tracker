<?php
// File: includes/export_categories_excel.php

// ✅ Correct path to vendor autoload
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/database.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Fetch categories from DB
try {
    $pdo = getDatabaseConnection();
    $stmt = $pdo->query("SELECT CategoryId, categoryName, type, created_at FROM categories ORDER BY CategoryId ASC");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// Create new Spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Categories');

// Set headers
$sheet->setCellValue('A1', 'ID')
      ->setCellValue('B1', 'Category Name')
      ->setCellValue('C1', 'Type')
      ->setCellValue('D1', 'Created At');

// Fill data
$row = 2;
foreach ($categories as $cat) {
    $sheet->setCellValue('A' . $row, $cat['CategoryId'])
          ->setCellValue('B' . $row, $cat['categoryName'])
          ->setCellValue('C' . $row, $cat['type'])
          ->setCellValue('D' . $row, $cat['created_at']);
    $row++;
}

// Set headers for download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="categories.xlsx"');
header('Cache-Control: max-age=0');

// Write file
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
