<!DOCTYPE html>
<html>
<head>
    <title>Cetak Laporan CMS</title>
</head>
<body>
    <h5 align="center"><img src="{{ asset('po-admin/assets/img/logo.png') }}" width="420px;" height="100px;"/></h5>
    <h3 align="center">Laporan Halaman Pengunjung CMS Madani 3.0</h3>
    <table border="1" cellspacing="0" cellpadding="1" width="100%">
     <thead>
        <tr nobr="true">
            <th width="5%" align="center">No</th>
            <th width="15%" align="center">URL</th>
            <th width="33%" align="center">User Agent</th>
            <th width="10%" align="center">Device</th>
            <th width="10%" align="center">Platform</th>
            <th width="8%" align="center">Browser</th>
            <th width="10%" align="center">IP</th>
            <th width="10%" align="center">Tanggal</th>
          </tr>
     </thead>
      <tbody>
        @php $i=1; @endphp
        @foreach($cetak as $s)
        <tr nobr="true">
            <td width="5%"  align="center"> {{ $i++ }} </td>
            <td width="15%" align="center">{{$s->url}}</td>
            <td width="33%" align="center">{{$s->useragent}}</td>
            <td width="10%" align="center">{{$s->device}}</td>
            <td width="10%" align="center">{{$s->platform}}</td>
            <td width="8%"  align="center">{{$s->browser}}</td>
            <td width="10%" align="center">{{$s->ip}}</td>
            <td width="10%" align="center">{{ Carbon\Carbon::parse($s->updated_at)->translatedFormat('l, d F Y, H:i:s') }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
</body>
</html>
