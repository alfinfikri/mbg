@extends(getTheme('layouts.app'))

@section('content')
<!-- Hero Section -->
<div id="notifTiket">
<section class="max-w-7xl mx-auto px-8 py-12 pt-32">
	<section class="mb-12">
		<div class="bg-gradient-to-br from-primary to-primary-container rounded-[1.5rem] p-10 text-on-primary relative overflow-hidden">
			<div class="relative z-10 max-w-xl">
				<span class="inline-block bg-secondary-container text-on-secondary-container px-3.5 py-0.5 rounded-full text-xs font-semibold mb-5">Pusat Layanan Terpadu</span>
				<h1 class="text-4xl font-extrabold tracking-tight mb-5">Suara Anda Adalah Nutrisi Bagi Program Kami.</h1>
				<p class="text-base opacity-90 leading-relaxed">Kami berkomitmen untuk terus meningkatkan kualitas layanan dan transparansi program Makan Bergizi Gratis di seluruh wilayah Indonesia.</p>
			</div>
			<div class="absolute right-0 bottom-0 top-0 w-1/3 hidden lg:block opacity-20">
				<img class="w-full h-full object-cover" data-alt="abstract professional geometric pattern with subtle depth and textures in shades of deep blue and glass" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBahoHw3nwGjzImf80Ml5brTtVd8uAVbhWgqHLYs5xV60gn4zG4wUOs-ZogsJZQqX_WrcxX0BO1GCTJUF7gp-PbUsnZIm5WgFza9gErQcDXGTI5-1xRrCugZ_ML_myFQhud_dlN6J2nl4uPADvYOeFVCUeAt8Zq4Wv_apqK7KZhBbEDbpIbA_R7El_ErH76yZ8EJZjFWVqFtPAmvUPOJm2qYFVK_4XNvbTaR_NGQ2TjLcQUZZuvPdl8Q33XV6aW5t7ZNxF2ElTKufQj" />
			</div>
		</div>
	</section>

	<!-- Main Interaction Area -->
	<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
		<!-- Left Column: Complaint Form -->
		<div class="lg:col-span-2 space-y-6">
			@if(session('kode_tiket'))
				<div class="bg-secondary-container/20 rounded-2xl p-7 mb-4 border-l-4 border-secondary relative overflow-hidden">
					<div class="flex items-start gap-3.5">
						<div class="bg-white p-2.5 rounded-xl shadow-sm">
							<span class="material-symbols-outlined text-secondary text-2xl">done_outline</span>
						</div>
						<div class="flex-grow">
							<h2 class="text-xl font-bold text-on-surface mb-1.5">✅ Pengaduan Berhasil Dikirim</h2>
							<p class="text-sm text-on-surface-variant mb-5">Simpan kode tiket berikut untuk tracking :</p>
							<span id="notifCopy" class="text-green-600 text-sm"></span>
							<div class="relative group max-w-lg">
								<input id="kodeTiket" 
									type="text" 
									value="{{ session('kode_tiket') }}" 
									readonly class="w-full bg-white/80 border-none rounded-xl px-5 py-3.5 pr-28 text-sm shadow-sm focus:ring-2 focus:ring-secondary/20 transition-all" />
								<button onclick="copyTiket()"  class="absolute right-1.5 top-1.5 bottom-1.5 bg-secondary text-on-secondary px-4 rounded-lg text-xs font-bold hover:opacity-90 transition-all">
									Copy
								</button>
							</div>
						</div>
					</div>
				</div>
			@endif
			
			<div class="bg-surface-container-lowest rounded-2xl p-7 shadow-[0px_12px_32px_rgba(24,28,30,0.06)] relative overflow-hidden">
				<div class="flex items-center gap-3.5 mb-7">
					<div class="bg-primary-fixed p-2.5 rounded-xl">
						<span class="material-symbols-outlined text-primary text-2xl">record_voice_over</span>
					</div>
					<div>
						<h2 class="text-xl font-bold text-on-surface">Kirim Pengaduan</h2>
						<p class="text-sm text-on-surface-variant">Laporkan kendala atau ketidaksesuaian di lapangan.</p>
					</div>
				</div>
				
				<form method="POST" action="{{ url('/contact/send') }}" enctype="multipart/form-data" class="space-y-5">
					@csrf

					<div class="grid grid-cols-1 md:grid-cols-2 gap-5">
						<div class="space-y-1.5">
							<label class="text-xs font-semibold ml-1">Nama Lengkap</label>
							<input name="nama" value="{{ old('nama') }}"
								class="w-full bg-surface-container-low border-none rounded-lg px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-primary/20 transition-all"
								placeholder="Masukkan nama lengkap" type="text" required/>
						</div>

						<div class="space-y-1.5">
							<label class="text-xs font-semibold ml-1">Nomor Telepon/WA</label>
							<input name="no_hp" value="{{ old('no_hp') }}"
								class="w-full bg-surface-container-low border-none rounded-lg px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-primary/20 transition-all"
								placeholder="0812..." type="tel" required/>
						</div>
					</div>

					<div class="space-y-1.5">
						<label class="text-xs font-semibold ml-1">Lokasi Kejadian</label>
						<input name="alamat" value="{{ old('alamat') }}"
							class="w-full bg-surface-container-low border-none rounded-lg px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-primary/20 transition-all"
							placeholder="Nama Sekolah atau Alamat" type="text" required/>
					</div>

					{{-- Kecamatan --}}
					<div class="grid grid-cols-1 md:grid-cols-2 gap-5">
						<div class="space-y-1.5">
							<label class="text-xs font-semibold ml-1">Kecamatan</label>
							<select id="parent_id" name="parent_id" required
								class="w-full bg-surface-container-low border-none rounded-lg px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-primary/20 transition-all">
								<option value="">-- Pilih Kecamatan --</option>
								@foreach($kecamatans as $id => $nama)
								<option value="{{ $id }}">{{ $nama }}</option>
								@endforeach
							</select>
						</div>

						{{-- Kelurahan --}}
						<div class="space-y-1.5">
							<label class="text-xs font-semibold ml-1">Kelurahan</label>
							<select id="wilayah_id" name="wilayah_id" required
								class="w-full bg-surface-container-low border-none rounded-lg px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-primary/20 transition-all">
								<option value="">-- Pilih Kelurahan --</option>
							</select>
						</div>
					</div>

					{{-- Foto --}}
					<div class="space-y-1.5">
						<label class="text-xs font-semibold text-on-surface ml-1">Unggah Foto</label>
						<input name="foto" class="w-full bg-surface-container-low rounded-lg px-3.5 py-2.5 text-sm file:mr-4 file:rounded-md file:border-0 file:bg-primary file:px-4 file:py-2 file:text-sm file:font-semibold file:text-on-primary hover:file:opacity-90 transition-all" type="file" accept="image/*" />
						<p class="text-[11px] text-on-surface-variant ml-1">Format foto: JPG, PNG, atau JPEG.</p>
					</div>

					{{-- Judul --}}
					<div class="space-y-1.5">
						<label class="text-xs font-semibold ml-1">Judul Aduan</label>
						<input name="judul_aduan" value="{{ old('judul_aduan') }}"
							class="w-full bg-surface-container-low border-none rounded-lg px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-primary/20 transition-all"
							placeholder="Masukkan judul aduan" type="text" required/>
					</div>

					{{-- Deskripsi --}}
					<div class="space-y-1.5">
						<label class="text-xs font-semibold ml-1">Detail Pengaduan</label>
						<textarea name="isi_aduan" 
							class="w-full bg-surface-container-low border-none rounded-lg px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-primary/20 transition-all"
							rows="3" required>{{ old('isi_aduan') }}</textarea>
					</div>

					<div class="flex items-center gap-4 pt-1">
						<button class="bg-primary text-on-primary px-7 py-2.5 rounded-lg text-sm font-bold hover:opacity-90 transition-all flex items-center gap-2" type="submit">
							Kirim Laporan <span class="material-symbols-outlined text-xs">send</span>
						</button>
						<span class="text-[11px] text-on-surface-variant italic">Data Anda akan dijaga kerahasiaannya sesuai regulasi.</span>
					</div>
				</form>

			</div>
		</div>
		<!-- Right Column: Contact Information -->
		<div class="space-y-6">
			<!-- Direct Contact Cards -->
			<div class="bg-surface-container-low rounded-2xl p-7 space-y-6">
				<h3 class="text-lg font-bold text-on-surface">Hubungi Kami</h3>
				<div class="flex items-start gap-3.5">
					<div class="bg-surface-container-lowest p-2.5 rounded-lg shadow-sm">
						<span class="material-symbols-outlined text-primary text-xl">location_on</span>
					</div>
					<div>
						<p class="text-[10px] font-bold uppercase tracking-wider text-on-surface-variant mb-0.5">Alamat Kantor</p>
						<p class="text-sm text-on-surface font-medium leading-relaxed">{{ getSetting('address') }}</p>
					</div>
				</div>
				<div class="flex items-start gap-3.5">
					<div class="bg-surface-container-lowest p-2.5 rounded-lg shadow-sm">
						<span class="material-symbols-outlined text-primary text-xl">call</span>
					</div>
					<div>
						<p class="text-[10px] font-bold uppercase tracking-wider text-on-surface-variant mb-0.5">Layanan Telepon</p>
						<p class="text-on-surface font-bold text-base">{{ getSetting('telephone') }}</p>
						<p class="text-[11px] text-on-surface-variant">Bebas Pulsa (08:00 - 17:00 WIB)</p>
					</div>
				</div>
				<div class="flex items-start gap-3.5">
					<div class="bg-surface-container-lowest p-2.5 rounded-lg shadow-sm">
						<span class="material-symbols-outlined text-primary text-xl">mail</span>
					</div>
					<div>
						<p class="text-[10px] font-bold uppercase tracking-wider text-on-surface-variant mb-0.5">Email Resmi</p>
						<p class="text-sm text-on-surface font-medium">{{ getSetting('email') }}</p>
						<p class="text-[11px] text-on-surface-variant">Respon rata-rata: 24 jam kerja</p>
					</div>
				</div>
			</div>
			<!-- Map Widget -->
			<div class="bg-surface-container-lowest rounded-2xl overflow-hidden shadow-sm h-52 relative group">

				<iframe 
					class="w-full h-full border-0"
					src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.9881339130693!2d106.19046167440871!3d-6.132295960122035!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e41f5429d179c8b%3A0x32577c59578709f9!2sSETDA%20KOTA%20SERANG!5e0!3m2!1sen!2sid!4v1776746912067!5m2!1sen!2sid"
					loading="lazy">
				</iframe>

				<div class="absolute inset-0 bg-primary/10 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
					<a href="https://www.google.com/maps?q=SETDA+KOTA+SERANG" 
					target="_blank"
					class="bg-white text-primary px-4 py-1.5 rounded-full text-xs font-bold shadow-lg flex items-center gap-1.5">
						Buka Map
						<span class="material-symbols-outlined text-xs">open_in_new</span>
					</a>
				</div>

			</div>
			<!-- Social Media -->
			<div class="p-3.5 flex justify-between items-center bg-white rounded-xl">
				<span class="text-xs font-semibold text-on-surface-variant">Ikuti Kami</span>
				<div class="flex gap-2.5">
					<button class="w-8 h-8 rounded-full bg-surface-container-low flex items-center justify-center text-primary hover:bg-primary hover:text-white transition-all">
						<span class="material-symbols-outlined text-lg">brand_family</span>
					</button>
					<button class="w-8 h-8 rounded-full bg-surface-container-low flex items-center justify-center text-primary hover:bg-primary hover:text-white transition-all">
						<span class="material-symbols-outlined text-lg">share</span>
					</button>
					<button class="w-8 h-8 rounded-full bg-surface-container-low flex items-center justify-center text-primary hover:bg-primary hover:text-white transition-all">
						<span class="material-symbols-outlined text-lg">play_circle</span>
					</button>
				</div>
			</div>
		</div>
	</div>
</section>
@endsection
@push('scripts')
<script>
	document.addEventListener("DOMContentLoaded", function () {
		let el = document.getElementById('notifTiket');
		if (el) {
			el.scrollIntoView({ behavior: 'smooth' });
		}
	});

	function copyTiket() {
		let input = document.getElementById("kodeTiket");

		input.select();
		document.execCommand("copy");

		document.getElementById("notifCopy").innerText = "✔ Copied!";
	}
	
	$(document).ready(function () {

		let oldKel = "{{ old('wilayah_id') }}";
		let oldKec = "{{ old('parent_id') }}";

		function loadKelurahan(id, selected = null) {
			$('#wilayah_id').html('<option>Loading...</option>');

			$.get('/get-kelurahan/' + id, function (data) {
				let options = '<option value="">-- Pilih Kelurahan --</option>';

				$.each(data, function (id, nama) {
					let selectedAttr = (selected == id) ? 'selected' : '';
					options += `<option value="${id}" ${selectedAttr}>${nama}</option>`;
				});

				$('#wilayah_id').html(options);
			});
		}

		// 🔥 event change
		$('#parent_id').on('change', function () {
			let id = $(this).val();

			if (id) {
				loadKelurahan(id);
			} else {
				$('#wilayah_id').html('<option value="">-- Pilih Kelurahan --</option>');
			}
		});

		// 🔥 load saat ada old value
		if (oldKec) {
			$('#parent_id').val(oldKec);
			loadKelurahan(oldKec, oldKel);
		}

	});
</script>
@endpush