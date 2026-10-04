<?php

namespace Database\Seeders;

use App\Models\Procurement;
use App\Models\Criterion;
use Illuminate\Database\Seeder;

class LaptopTierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tiers = [
            [
                'name' => 'Tier 1: Basic Operational & Field',
                'description' => "Standar Laptop Tier 1 untuk operasional lapangan dan administratif dasar.\n\n"
                    . "• Divisi/Jabatan: SPV Warehouse, Kepala Gudang Produksi, Kepala Gudang Pusat, Kepala SPV, CS (Customer Service)\n"
                    . "• Estimasi Harga: Rp 6.599.000 - Rp 8.400.000\n"
                    . "• Rekomendasi/Preferensi Merk: Asus, Lenovo, Advance, HP, Acer\n"
                    . "• Catatan Kunci: Mandatori SSD NVMe. Hindari HDD. Wajib OS Windows Original.",
                'status' => 'active',
                'criteria' => [
                    [
                        'name'   => 'Generasi Prosesor & Chipset',
                        'target' => 'Intel Core i3 Gen 12/13 (i3-1215U / i3-1315U) / AMD Ryzen 3 7000 Series (Zen 3+)',
                        'weight' => 20,
                        'order'  => 1,
                    ],
                    [
                        'name'   => 'Arsitektur RAM & Speed',
                        'target' => 'RAM 8 GB DDR4/DDR5',
                        'weight' => 15,
                        'order'  => 2,
                    ],
                    [
                        'name'   => 'Spesifikasi Storage & Speed (NVMe)',
                        'target' => '256 GB SSD NVMe M.2',
                        'weight' => 15,
                        'order'  => 3,
                    ],
                    [
                        'name'   => 'Panel Layar & Color Gamut',
                        'target' => '14" Full HD (1920x1080) TN/IPS Anti-glare, 45% NTSC, 250 nits',
                        'weight' => 10,
                        'order'  => 4,
                    ],
                    [
                        'name'   => 'Konektivitas & Port',
                        'target' => 'Wi-Fi 5/6 (802.11ax), BT 5.1, USB 3.2 Gen1, USB-C (Data), HDMI 1.4b, Combo Audio',
                        'weight' => 10,
                        'order'  => 5,
                    ],
                    [
                        'name'   => 'OS & Keamanan',
                        'target' => 'Windows 11 Home 64-bit Original, TPM 2.0, Kensington Lock',
                        'weight' => 15,
                        'order'  => 6,
                    ],
                    [
                        'name'   => 'Estimasi Harga & Rekomendasi Merk',
                        'target' => 'Rp 6.599.000 - Rp 8.400.000 (Asus, Lenovo, Advance, HP, Acer)',
                        'weight' => 15,
                        'order'  => 7,
                    ],
                ],
            ],
            [
                'name' => 'Tier 2: Business & Finance',
                'description' => "Standar Laptop Tier 2 untuk kebutuhan bisnis, analisis data, dan operasional finansial.\n\n"
                    . "• Divisi/Jabatan: Procurement, Purchasing, QC Supply Chain, Operational Compliance, Business Development, Human Resource, General Affairs, Learning & Development, Finance, Accounting, Marketing Operational, Marketing RND, CRM, BIP, Audit, IT Operational, Product Development\n"
                    . "• Estimasi Harga: Rp 7.750.000 - Rp 10.100.000\n"
                    . "• Rekomendasi/Preferensi Merk: Asus, Lenovo, Advance, HP, Acer\n"
                    . "• Catatan Kunci: RAM 16GB sangat disarankan untuk mengolah file Excel/Spreadsheet besar. Disarankan Full Keyboard / Numpad untuk Finance/Audit.",
                'status' => 'active',
                'criteria' => [
                    [
                        'name'   => 'Generasi Prosesor & Chipset',
                        'target' => 'Intel Core i5 Gen 12/13/14 (i5-1235U / i5-1340P) / AMD Ryzen 5 7000/8000 Series',
                        'weight' => 20,
                        'order'  => 1,
                    ],
                    [
                        'name'   => 'Arsitektur RAM & Speed',
                        'target' => 'DDR4 3200 MHz / DDR5 5200-5600 MHz (8GB, disarankan 16GB)',
                        'weight' => 15,
                        'order'  => 2,
                    ],
                    [
                        'name'   => 'Spesifikasi Storage & Speed (NVMe)',
                        'target' => 'M.2 NVMe PCIe Gen 4.0 x4 (Read Speed 3.500-5.000 MB/s, 512 GB)',
                        'weight' => 15,
                        'order'  => 3,
                    ],
                    [
                        'name'   => 'Panel Layar & Color Gamut',
                        'target' => '14" - 15.6" Full HD IPS Anti-glare, 300 nits (Disarankan Full Keyboard / Numpad)',
                        'weight' => 10,
                        'order'  => 4,
                    ],
                    [
                        'name'   => 'Konektivitas & Port',
                        'target' => 'Wi-Fi 6/6E, BT 5.2, USB-C Full Function (PD & DisplayPort), HDMI 2.0, RJ-45 Gigabit LAN',
                        'weight' => 10,
                        'order'  => 5,
                    ],
                    [
                        'name'   => 'OS & Keamanan',
                        'target' => 'Windows 11 Pro 64-bit Original, TPM 2.0, Fingerprint, Privacy Shutter Webcam',
                        'weight' => 15,
                        'order'  => 6,
                    ],
                    [
                        'name'   => 'Estimasi Harga & Rekomendasi Merk',
                        'target' => 'Rp 7.750.000 - Rp 10.100.000 (Asus, Lenovo, Advance, HP, Acer)',
                        'weight' => 15,
                        'order'  => 7,
                    ],
                ],
            ],
            [
                'name' => 'Tier 3: Power User, Tech & Creative',
                'description' => "Standar Laptop Tier 3 untuk kebutuhan komputasi berat, software engineer, dan desainer kreatif.\n\n"
                    . "• Divisi/Jabatan: IT Development, UI/UX, Product Development, Social Media Marketing\n"
                    . "• Estimasi Harga: Rp 13.846.420 - Rp 24.599.000\n"
                    . "• Rekomendasi/Preferensi Merk: MSI, TUF, Acer, Apple, Lenovo\n"
                    . "• Catatan Kunci: Membutuhkan akurasi warna tinggi. IT Dev & Ops butuh RAM 16-32GB untuk Local Docker, Virtualization & DB Query.",
                'status' => 'active',
                'criteria' => [
                    [
                        'name'   => 'Generasi Prosesor & Chipset',
                        'target' => 'Intel Core i7 Gen 13/14 (i7-13700H) / AMD Ryzen 7 7000/8000 / Apple M2-M3 Chip',
                        'weight' => 20,
                        'order'  => 1,
                    ],
                    [
                        'name'   => 'Arsitektur RAM & Speed',
                        'target' => 'DDR5 5600 MHz / LPDDR5X / Unified Memory (16 GB - 32 GB Dual Channel)',
                        'weight' => 15,
                        'order'  => 2,
                    ],
                    [
                        'name'   => 'Spesifikasi Storage & Speed (NVMe)',
                        'target' => 'M.2 NVMe PCIe Gen 4.0 x4 High-Speed (Read Speed 5.000-7.000 MB/s, 512 GB - 1 TB)',
                        'weight' => 15,
                        'order'  => 3,
                    ],
                    [
                        'name'   => 'Panel Layar & Color Gamut',
                        'target' => '14" - 16" FHD+/2.5K IPS/OLED/Retina, 100% sRGB / 99% DCI-P3, 400-500 nits',
                        'weight' => 15,
                        'order'  => 4,
                    ],
                    [
                        'name'   => 'Konektivitas & Port',
                        'target' => 'Wi-Fi 6E/7, BT 5.3, Thunderbolt 4 / USB4, HDMI 2.1, RJ-45 Gigabit LAN, SD Card Reader',
                        'weight' => 10,
                        'order'  => 5,
                    ],
                    [
                        'name'   => 'OS & Keamanan',
                        'target' => 'Windows 11 Pro 64-bit / macOS Sonoma, TPM 2.0 / Apple Security Enclave, IR Camera (Windows Hello)',
                        'weight' => 15,
                        'order'  => 6,
                    ],
                    [
                        'name'   => 'Estimasi Harga & Rekomendasi Merk',
                        'target' => 'Rp 13.846.420 - Rp 24.599.000 (MSI, TUF, Acer, Apple, Lenovo)',
                        'weight' => 10,
                        'order'  => 7,
                    ],
                ],
            ],
        ];

        foreach ($tiers as $tierData) {
            $criteriaList = $tierData['criteria'];
            unset($tierData['criteria']);

            $procurement = Procurement::updateOrCreate(
                ['name' => $tierData['name']],
                $tierData
            );

            // Re-sync criteria
            $procurement->criteria()->delete();

            foreach ($criteriaList as $crit) {
                $procurement->criteria()->create($crit);
            }
        }
    }
}
