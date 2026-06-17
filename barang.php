<?php
abstract class Barang {
    protected $id, $deskripsi, $kategori;
    public function __construct($id, $deskripsi, $kategori = '') {
        $this->id = $id; $this->deskripsi = $deskripsi; $this->kategori = $kategori;
    }
    abstract public function validasi(string $input, string $asli): bool;
    abstract public function labelBukti(): string;
    abstract public function placeholderBukti(): string;
}
class BarangElektronik extends Barang {
    public function validasi(string $input, string $asli): bool { return trim($input) === trim($asli); }
    public function labelBukti(): string { return 'Nomor IMEI / Serial Number'; }
    public function placeholderBukti(): string { return 'Contoh: 354823109012345'; }
}
class BarangDokumen extends Barang {
    public function validasi(string $input, string $asli): bool { return strtolower(trim($input)) === strtolower(trim($asli)); }
    public function labelBukti(): string { return 'Nama Pemilik (sesuai dokumen)'; }
    public function placeholderBukti(): string { return 'Contoh: Budi Santoso'; }
}
class BarangUmum extends Barang {
    public function validasi(string $input, string $asli): bool {
        return strtolower(preg_replace('/\s+/',' ',trim($input))) === strtolower(preg_replace('/\s+/',' ',trim($asli)));
    }
    public function labelBukti(): string { return 'Ciri Khas Barang'; }
    public function placeholderBukti(): string { return 'Contoh: Dompet kulit coklat, ada foto keluarga di dalam'; }
}
function buatObjekBarang($id, $deskripsi, $kategori): Barang {
    switch (strtolower($kategori)) {
        case 'elektronik': return new BarangElektronik($id, $deskripsi, $kategori);
        case 'dokumen':    return new BarangDokumen($id, $deskripsi, $kategori);
        default:           return new BarangUmum($id, $deskripsi, $kategori);
    }
}
