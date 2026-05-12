@extends('layouts.email')

@section('content')
<font face="'Source Sans Pro', sans-serif" color="#1a1a1a" style="font-size: 24px; line-height: 30px;">
	<span style="font-family: 'Source Sans Pro', Arial, Tahoma, Geneva, sans-serif; color: #1a1a1a; font-size: 24px; line-height: 30px; font-weight: 700;">Aduan Baru Masuk</span>
</font>

<div style="height: 18px; line-height: 18px; font-size: 16px;">&nbsp;</div>

<font face="'Source Sans Pro', sans-serif" color="#333333" style="font-size: 16px; line-height: 24px;">
	<span style="font-family: 'Source Sans Pro', Arial, Tahoma, Geneva, sans-serif; color: #333333; font-size: 16px; line-height: 24px;">
		Ada aduan baru yang masuk ke sistem MBG dan perlu ditinjau oleh superadmin.
	</span>
</font>

<div style="height: 22px; line-height: 22px; font-size: 20px;">&nbsp;</div>

<table cellpadding="0" cellspacing="0" border="0" width="100%" style="border-collapse: collapse; font-family: 'Source Sans Pro', Arial, Tahoma, Geneva, sans-serif; font-size: 15px; color: #333333;">
	<tr>
		<td width="150" style="padding: 8px 0; font-weight: 700; vertical-align: top;">Kode Tiket</td>
		<td style="padding: 8px 0; vertical-align: top;">{{ $aduan->kode_tiket }}</td>
	</tr>
	<tr>
		<td width="150" style="padding: 8px 0; font-weight: 700; vertical-align: top;">Pelapor</td>
		<td style="padding: 8px 0; vertical-align: top;">{{ $aduan->nama }}</td>
	</tr>
	<tr>
		<td width="150" style="padding: 8px 0; font-weight: 700; vertical-align: top;">No HP</td>
		<td style="padding: 8px 0; vertical-align: top;">{{ $aduan->no_hp ?: '-' }}</td>
	</tr>
	<tr>
		<td width="150" style="padding: 8px 0; font-weight: 700; vertical-align: top;">Tanggal</td>
		<td style="padding: 8px 0; vertical-align: top;">{{ $aduan->tgl_aduan ? \Carbon\Carbon::parse($aduan->tgl_aduan)->format('d-m-Y H:i') : '-' }}</td>
	</tr>
	<tr>
		<td width="150" style="padding: 8px 0; font-weight: 700; vertical-align: top;">Judul</td>
		<td style="padding: 8px 0; vertical-align: top;">{{ $aduan->judul_aduan }}</td>
	</tr>
	<tr>
		<td width="150" style="padding: 8px 0; font-weight: 700; vertical-align: top;">Lokasi</td>
		<td style="padding: 8px 0; vertical-align: top;">{{ $aduan->alamat ?: '-' }}</td>
	</tr>
	<tr>
		<td width="150" style="padding: 8px 0; font-weight: 700; vertical-align: top;">Isi Aduan</td>
		<td style="padding: 8px 0; vertical-align: top;">{!! nl2br(e($aduan->isi_aduan)) !!}</td>
	</tr>
</table>

<div style="height: 24px; line-height: 24px; font-size: 22px;">&nbsp;</div>

<a href="{{ url('dashboard/aduans/'.\Vinkla\Hashids\Facades\Hashids::encode($aduan->id)) }}" target="_blank" style="display: inline-block; padding: 12px 18px; background: #0b5ed7; color: #ffffff; text-decoration: none; border-radius: 4px; font-family: 'Source Sans Pro', Arial, Tahoma, Geneva, sans-serif; font-size: 15px; font-weight: 700;">
	Lihat Detail Aduan
</a>
@endsection
