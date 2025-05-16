            </div>
		</div>

		<div
			x-show="showModal"
			class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
		>
			<div class="bg-white rounded-lg w-1/2" @click.outside="showModal = false">
				<div class="p-4 border-b flex justify-between items-center">
					<h3 class="text-lg font-semibold" x-text="modalTitle"></h3>
					<button
						@click="showModal = false"
						class="text-gray-500 hover:text-gray-700"
					>
						<i class="fas fa-times"></i>
					</button>
				</div>
				<div class="p-4">
					<div id="modalContent"></div>
				</div>
				<div class="p-4 border-t flex justify-end space-x-2">
					<button
						@click="showModal = false"
						class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded"
					>
						Batal
					</button>
					<button
						@click="submitForm"
						class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
					>
						Simpan
					</button>
				</div>
			</div>
		</div>
		
		<script>
			document.addEventListener("DOMContentLoaded", function () {
				const currentPath = window.location.pathname;

				const menuItems = document.querySelectorAll(".menu-item");

				menuItems.forEach((item) => {
				const linkPath = item.getAttribute("href");

				if (
					currentPath === linkPath ||
					(linkPath !== "/dashboard" && currentPath.startsWith(linkPath + "/"))
				) {
					item.classList.add("bg-blue-100", "text-blue-700");
					item.classList.remove("text-gray-700");

					const icon = item.querySelector("i");
					if (icon) icon.classList.add("text-blue-600");
				}
				});
			});
		</script>
		
		<?php if (isset($_SESSION['success'])): ?>
			<script>
			Swal.fire({
				icon: 'success',
				title: 'Berhasil!',
				text: '<?= $_SESSION['success'] ?>',
				timer: 2000
			});
			<?php unset($_SESSION['success']); ?>
			</script>
			<?php endif; ?>

			<?php if (isset($_SESSION['error'])): ?>
			<script>
			Swal.fire({
				icon: 'error',
				title: 'Gagal!',
				text: '<?= $_SESSION['error'] ?>'
			});
			<?php unset($_SESSION['error']); ?>
			</script>
		<?php endif; ?>

	</body>
</html>