@extends('layouts.admin')

@section('title', 'Edit Lokasi Kerja')
@section('page_title', 'Edit Lokasi: ' . $location->name)

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
<style>
    #map { height: 300px; width: 100%; border-radius: 5px; margin-bottom: 15px; }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <form action="{{ route('admin.locations.update', $location) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Lokasi</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $location->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Pilih Titik di Peta</label>
                        <div id="map"></div>
                        <small class="text-muted">Klik pada peta untuk menentukan latitude dan longitude secara otomatis.</small>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Latitude</label>
                            <input type="text" id="latitude" name="latitude" class="form-control @error('latitude') is-invalid @enderror" value="{{ old('latitude', $location->latitude) }}" readonly required>
                            @error('latitude')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Longitude</label>
                            <input type="text" id="longitude" name="longitude" class="form-control @error('longitude') is-invalid @enderror" value="{{ old('longitude', $location->longitude) }}" readonly required>
                            @error('longitude')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Radius Presensi (Meter)</label>
                        <input type="number" id="radius" name="radius" class="form-control @error('radius') is-invalid @enderror" value="{{ old('radius', $location->radius) }}" min="1" required>
                        <small class="text-muted">Jarak maksimal karyawan diizinkan melakukan absen dari titik kordinat.</small>
                        @error('radius')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    <a href="{{ route('admin.locations.index') }}" class="btn btn-default float-end">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    var currentLat = {{ old('latitude', $location->latitude ?: '-6.2088') }};
    var currentLng = {{ old('longitude', $location->longitude ?: '106.8456') }};
    
    var map = L.map('map').setView([currentLat, currentLng], 15);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap'
    }).addTo(map);

    // Fix map rendering issues in some bootstrap layouts
    setTimeout(function() { map.invalidateSize(); }, 500);

    var radiusInput = document.getElementById('radius');
    var currentRadius = parseInt(radiusInput.value) || 50;

    var marker = L.marker([currentLat, currentLng]).addTo(map);
    var circle = L.circle([currentLat, currentLng], {
        color: 'red',
        fillColor: '#f03',
        fillOpacity: 0.2,
        radius: currentRadius
    }).addTo(map);

    function updateMap(lat, lng) {
        var radius = parseInt(radiusInput.value) || 50;
        var latlng = [lat, lng];

        marker.setLatLng(latlng);
        circle.setLatLng(latlng);
        circle.setRadius(radius);

        document.getElementById('latitude').value = lat;
        document.getElementById('longitude').value = lng;
    }

    var geocoder = L.Control.geocoder({
        defaultMarkGeocode: false
    })
    .on('markgeocode', function(e) {
        var latlng = e.geocode.center;
        map.setView(latlng, 16);
        updateMap(latlng.lat, latlng.lng);
    })
    .addTo(map);

    map.on('click', function(e) {
        updateMap(e.latlng.lat, e.latlng.lng);
    });

    radiusInput.addEventListener('input', function() {
        updateMap(marker.getLatLng().lat, marker.getLatLng().lng);
    });
});
</script>
@endpush
