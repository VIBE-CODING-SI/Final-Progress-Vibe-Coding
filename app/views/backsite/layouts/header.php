<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<title>CPS Dashboard | User</title>
		<!-- Favicon -->
		<link rel="icon" href="/public/assets/favicon.png" type="image/png">
		
		<!-- Tailwind CSS CDN -->
		<script src="https://cdn.tailwindcss.com"></script>

		<!-- Tambahkan ini di bawah Tailwind CSS CDN -->
		<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>		

		<!-- Font Awesome CDN -->
		<link
			rel="stylesheet"
			href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
		/>

		
		<!-- SweetAlert2 -->
		<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
		
		<style>
			[x-cloak] { display: none !important; }
			</style>

		<!-- DataTables CSS -->
		<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

		<!-- Tambahkan di bawah DataTables CSS -->
		<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">

		<!-- Alpine.js untuk interaktivitas -->
		<script
			defer
			src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"
		></script>

		<!-- DataTables JS -->
		<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
	</head>
	<body class="bg-gray-100" x-data="{ sidebarOpen: true, profileOpen: false }">
		<div class="min-h-screen flex">