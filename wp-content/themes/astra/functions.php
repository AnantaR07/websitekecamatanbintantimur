<?php
/**
 * Astra functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Astra
 * @since 1.0.0
 */

// Kode fungsi Astra di atas tetap dibiarkan seperti sebelumnya...

/**
 * Fungsi untuk menampilkan data luas wilayah kelurahan
 */

 if (!function_exists('format_text')) {
    // Fungsi untuk mengganti koma dengan newline tanpa menampilkan koma
    function format_text($text) {
        return str_replace(',', '<br>', $text);
    }
}

function data_luas_wilayah_kelurahan() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_luas_wilayah_kelurahan';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
            .custom-table {
                width: 100%;
                border-collapse: collapse;
                margin: 20px 0;
                font-size: 9px;
                text-align: left;
            }
            .custom-table th{
                font-size: 10px;
            }    
            .custom-table td{
                font-size: 10px;
            }  
            .custom-table th, .custom-table td {
                padding: 12px;
            }
            .custom-table thead {
                background-color: #f2f2f2;
                color: #333;
            }
            .custom-table tr {
                border-bottom: 1px solid #ddd;
            }
            /* Media Query untuk layar kecil */
            @media (max-width: 768px) {
                .custom-table {
                    font-size: 10px; /* Mengubah ukuran font */
                }
            }
            @media (max-width: 480px) {
                .custom-table {
                    font-size: 10px; /* Mengubah ukuran font lebih kecil */
                }
            }
        </style>';

        $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
        $output .= '<table class="custom-table">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Nama Kelurahan</th>';
        $output .= '<th style="padding:12px;">Luas Wilayah</th>';
        $output .= '<th style="padding:12px;">Populasi</th>';
                                    // Tambahkan kolom "Aksi" jika admin login
                                    if (current_user_can('administrator')) {
                                        $output .= '<th style="padding:12px;">Aksi</th>';
                                    }
        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->kelurahan) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->luas_wilayah) . '%</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->presentasi) . ' %</td>';
                                    // Jika admin login, tampilkan tombol Edit
                                    if (current_user_can('administrator')) {
                                        $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-data-wilayah-kelurahan-bintan-timur?id=' . $row->id . '">Edit</a></td>';
                        
                                    }
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }
    if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-data-wilayah-kelurahan-bintan-timur" class="tambah-button">Tambah Data wilayah Kelurahan</a>';
        $output .= '</div>';
    }

    return $output;
}

function edit_data_luas_wilayah_kelurahan() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_luas_wilayah_kelurahan'; // Nama tabel yang benar

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_luas_wilayah_kelurahan',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/wilayah-kecamatan/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
            // Jika form disubmit untuk update
            if (isset($_POST['submit'])) {
                // Validasi dan sanitasi input dari form
                $kelurahan = sanitize_text_field($_POST['kelurahan']);
                $luas_wilayah = floatval($_POST['luas_wilayah']);
                $presentasi = sanitize_text_field($_POST['presentasi']);

                // Update data ke database
                $wpdb->update(
                    'data_luas_wilayah_kelurahan',
                    array(
                        'kelurahan' => $kelurahan,
                        'luas_wilayah' => $luas_wilayah,
                        'presentasi' => $presentasi,
                    ),
                    array('id' => $id),  // Syarat untuk update berdasarkan ID
                    array('%s', '%.2f', '%.2f'),  // Format tipe data
                    array('%d')  // Format untuk ID
                );

                // Redirect setelah data berhasil diupdate
                echo '<script type="text/javascript">
                    alert("Data berhasil diupdate.");
                    window.location.href = "http://websitekecamatanbintantimur.test/wilayah-kecamatan/";
                </script>';
                exit;
            }

            // Tampilkan form untuk mengedit data
            return '
                <form method="post">
                    <label for="uraian">Kelurahan:</label>
                    <input type="text" name="kelurahan" value="' . esc_attr($result->kelurahan) . '"><br>
                    <label for="uraian">Jumlah Luas wilayah:</label>
                    <input type="number" name="luas_wilayah" value="' . esc_attr($result->luas_wilayah) . '" step="0.01" inputmode="decimal" pattern="[0-9]+(\.[0-9]{1,2})?" /><br>
                    <label for="uraian">Presentasi Popoluasi:</label>
                    <input type="number" name="presentasi" value="' . esc_attr($result->presentasi) . '" step="0.01" inputmode="decimal" pattern="[0-9]+(\.[0-9]{1,2})?" /><br>
                    <input type="submit" name="submit" value="Update Data" class="update-button">
                    <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
                </form>';
        } else {
            // Jika data tidak ditemukan
            return '<p>Data tidak ditemukan.</p>';
        }
    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_data_luas_wilayah_kelurahan', 'edit_data_luas_wilayah_kelurahan');


function tambah_data_luas_wilayah_kelurahan() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_luas_wilayah_kelurahan';

    if (isset($_POST['submit'])) {
        $kelurahan = sanitize_text_field($_POST['kelurahan']);
        $luas_wilayah = floatval($_POST['luas_wilayah']);
        $presentasi = sanitize_text_field($_POST['presentasi']);

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_luas_wilayah_kelurahan',
            array(
                'nomor' => $new_nomor,
                'kelurahan' => $kelurahan,
                'luas_wilayah' => $luas_wilayah,
                'presentasi' => $presentasi,
            ),
            array('%d','%s', '%.2f', '%.2f')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/wilayah-kecamatan/";
        </script>';
        exit;
    }

    return '
        <form method="post">
        <label for="uraian">Kelurahan:</label>
        <input type="text" name="kelurahan" value="' . esc_attr($result->kelurahan) . '"><br>
        <label for="uraian">Jumlah Luas wilayah:</label>
        <input type="number" name="luas_wilayah" value="' . esc_attr($result->luas_wilayah) . '" step="0.01" inputmode="decimal" pattern="[0-9]+(\.[0-9]{1,2})?" /><br>
        <label for="uraian">Presentasi Popoluasi:</label>
        <input type="number" name="presentasi" value="' . esc_attr($result->presentasi) . '" step="0.01" inputmode="decimal" pattern="[0-9]+(\.[0-9]{1,2})?" /><br>
        <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_luas_wilayah_kelurahan', 'tambah_data_luas_wilayah_kelurahan');


function  data_jarak_tempuh() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_jarak_tempuh';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
            text-align: left;
        }
        .custom-table th{
            font-size: 10px;
        }    
        .custom-table td{
            font-size: 10px;
        }  
        .custom-table th, .custom-table td {
            padding: 12px;
        }
        .custom-table thead {
            background-color: #f2f2f2;
            color: #333;
        }
        .custom-table tr {
            border-bottom: 1px solid #ddd;
        }
        /* Media Query untuk layar kecil */
        @media (max-width: 768px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font */
            }
        }
        @media (max-width: 480px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font lebih kecil */
            }
        }
        </style>';

        $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
        $output .= '<table class="custom-table">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Orbitasi, Jarak Dan Waktu Tempuh</th>';
        $output .= '<th style="padding:12px;">Km²</th>';
        $output .= '<th style="padding:12px;">Jam/Menit</th>';
                                    // Tambahkan kolom "Aksi" jika admin login
                                    if (current_user_can('administrator')) {
                                        $output .= '<th style="padding:12px;">Aksi</th>';
                                    }
        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->lokasi_tempuh) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->km) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->waktu_tempuh) . '</td>';
                                    // Jika admin login, tampilkan tombol Edit
                                    if (current_user_can('administrator')) {
                                        $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-data-orbitasi-jarak-dan-waktu-tempuh?id=' . $row->id . '">Edit</a></td>';
                        
                                    }
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }
    if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-data-orbitasi-jarak-dan-waktu-tempuh" class="tambah-button">Tambah Data Jarak Tempuh</a>';
        $output .= '</div>';
    }

    return $output;
}

function edit_data_jarak_tempuh() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_jarak_tempuh'; // Nama tabel yang benar

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_jarak_tempuh',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/wilayah-kecamatan/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
            // Jika form disubmit untuk update
            if (isset($_POST['submit'])) {
                // Validasi dan sanitasi input dari form
                $lokasi_tempuh = sanitize_text_field($_POST['lokasi_tempuh']);
                $km = floatval($_POST['km']);
                $waktu_tempuh = intval($_POST['waktu_tempuh']);

                // Update data ke database
                $wpdb->update(
                    'data_jarak_tempuh',
                    array(
                        'lokasi_tempuh' => $lokasi_tempuh,
                        'km' => $km,
                        'waktu_tempuh' => $waktu_tempuh,
                    ),
                    array('id' => $id),  // Syarat untuk update berdasarkan ID
                    array('%s', '%.2f', '%d'),  // Format tipe data
                    array('%d')  // Format untuk ID
                );

                // Redirect setelah data berhasil diupdate
                echo '<script type="text/javascript">
                    alert("Data berhasil diupdate.");
                    window.location.href = "http://websitekecamatanbintantimur.test/wilayah-kecamatan/";
                </script>';
                exit;
            }

            // Tampilkan form untuk mengedit data
            return '
                <form method="post">
                    <label for="uraian">Orbitasi, Jarak Dan Waktu Tempuh Dari Wilayah:</label>
                    <input type="text" name="lokasi_tempuh" value="' . esc_attr($result->lokasi_tempuh) . '"><br>
                    <label for="uraian">Jumlah Km²:</label>
                    <input type="number" name="km" value="' . esc_attr($result->km) . '" step="0.01" inputmode="decimal" pattern="[0-9]+(\.[0-9]{1,2})?" /><br>
                    <label for="uraian">Waktu Tempuh (Jam/Menit):</label>
                    <input type="number" name="waktu_tempuh" value="' . esc_attr($result->waktu_tempuh) . '" inputmode="numeric" pattern="\d*"><br>
                    <input type="submit" name="submit" value="Update Data" class="update-button">
                    <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
                </form>';
        } else {
            // Jika data tidak ditemukan
            return '<p>Data tidak ditemukan.</p>';
        }
    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_data_jarak_tempuh', 'edit_data_jarak_tempuh');


function tambah_data_jarak_tempuh() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_jarak_tempuh';

    if (isset($_POST['submit'])) {
        $lokasi_tempuh = sanitize_text_field($_POST['lokasi_tempuh']);
        $km = floatval($_POST['km']);
        $waktu_tempuh = intval($_POST['waktu_tempuh']);

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_jarak_tempuh',
            array(
                'nomor' => $new_nomor,
                'lokasi_tempuh' => $lokasi_tempuh,
                'km' => $km,
                'waktu_tempuh' => $waktu_tempuh,
            ),
            array('%d','%s', '%.2f', '%d')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/wilayah-kecamatan/";
        </script>';
        exit;
    }

    return '
        <form method="post">
        <label for="uraian">Orbitasi, Jarak Dan Waktu Tempuh Dari Wilayah:</label>
        <input type="text" name="lokasi_tempuh" value="' . esc_attr($result->lokasi_tempuh) . '"><br>
        <label for="uraian">Jumlah Km²:</label>
        <input type="number" name="km" value="' . esc_attr($result->km) . '" step="0.01" inputmode="decimal" pattern="[0-9]+(\.[0-9]{1,2})?" /><br>
        <label for="uraian">Waktu Tempuh (Jam/Menit):</label>
        <input type="number" name="waktu_tempuh" value="' . esc_attr($result->waktu_tempuh) . '" inputmode="numeric" pattern="\d*"><br>
        <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_jarak_tempuh', 'tambah_data_jarak_tempuh');


function  data_kondisi_geografis() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_kondisi_geografis';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
            text-align: left;
        }
        .custom-table th{
            font-size: 10px;
        }    
        .custom-table td{
            font-size: 10px;
        }  
        .custom-table th, .custom-table td {
            padding: 12px;
        }
        .custom-table thead {
            background-color: #f2f2f2;
            color: #333;
        }
        .custom-table tr {
            border-bottom: 1px solid #ddd;
        }
        /* Media Query untuk layar kecil */
        @media (max-width: 768px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font */
            }
        }
        @media (max-width: 480px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font lebih kecil */
            }
        }
        </style>';

        $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
        $output .= '<table class="custom-table">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Bulan</th>';
        $output .= '<th style="padding:12px;">Arah( ° )</th>';
        $output .= '<th style="padding:12px;">Kecepatan</th>';
        $output .= '<th style="padding:12px;">Rata-Rata Temperatur Udara(°C )</th>';
                                    // Tambahkan kolom "Aksi" jika admin login
                                    if (current_user_can('administrator')) {
                                        $output .= '<th style="padding:12px;">Aksi</th>';
                                    }
        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->bulan) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->arah) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->kecepatan) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->temperatur_udara) . '</td>';
                                    // Jika admin login, tampilkan tombol Edit
                                    if (current_user_can('administrator')) {
                                        $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-data-kondisi-geografis-kecamatan-bintan-timur?id=' . $row->id . '">Edit</a></td>';
                        
                                    }
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }
    if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-data-kondisi-geografis-kecamatan-bintan-timur" class="tambah-button">Tambah Data Geografis Kecamatan Bintan Timur</a>';
        $output .= '</div>';
    }

    return $output;
}

function edit_data_geografis_kecamatan_bintan_timur() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_kondisi_geografis'; // Nama tabel yang benar

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_kondisi_geografis',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/wilayah-kecamatan/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
            // Jika form disubmit untuk update
            if (isset($_POST['submit'])) {
                // Validasi dan sanitasi input dari form
                $bulan = sanitize_text_field($_POST['bulan']);
                $arah = intval($_POST['arah']);
                $kecepatan = intval($_POST['kecepatan']);
                $temperatur_udara = floatval($_POST['temperatur_udara']);

                // Update data ke database
                $wpdb->update(
                    'data_kondisi_geografis',
                    array(
                        'bulan' => $bulan,
                        'arah' => $arah,
                        'kecepatan' => $kecepatan,
                        'temperatur_udara' => $temperatur_udara,
                        
                    ),
                    array('id' => $id),  // Syarat untuk update berdasarkan ID
                    array('%s', '%d', '%d', '%.2f'),  // Format tipe data
                    array('%d')  // Format untuk ID
                );

                // Redirect setelah data berhasil diupdate
                echo '<script type="text/javascript">
                    alert("Data berhasil diupdate.");
                    window.location.href = "http://websitekecamatanbintantimur.test/wilayah-kecamatan/";
                </script>';
                exit;
            }

            // Tampilkan form untuk mengedit data
            return '
                <form method="post">
                    <label for="uraian">Bulan:</label>
                    <input type="text" name="bulan" value="' . esc_attr($result->bulan) . '"><br>
                    <label for="uraian">Arah Angin:</label>
                    <input type="number" name="arah" value="' . esc_attr($result->arah) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Kecepatan:</label>
                    <input type="number" name="kecepatan" value="' . esc_attr($result->kecepatan) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Temperatur Udara:</label>
                    <input type="number" name="temperatur_udara" value="' . esc_attr($result->temperatur_udara) . '" step="0.01" inputmode="decimal" pattern="[0-9]+(\.[0-9]{1,2})?" /><br>
                    <input type="submit" name="submit" value="Update Data" class="update-button">
                    <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
                </form>';
        } else {
            // Jika data tidak ditemukan
            return '<p>Data tidak ditemukan.</p>';
        }
    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_data_geografis_kecamatan_bintan_timur', 'edit_data_geografis_kecamatan_bintan_timur');


function tambah_data_geografis_kecamatan_bintan_timur() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_kondisi_geografis';

    if (isset($_POST['submit'])) {
        $bulan = sanitize_text_field($_POST['bulan']);
        $arah = intval($_POST['arah']);
        $kecepatan = intval($_POST['kecepatan']);
        $temperatur_udara = floatval($_POST['temperatur_udara']);

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_kondisi_geografis',
            array(
                'nomor' => $new_nomor,
                'bulan' => $bulan,
                'arah' => $arah,
                'kecepatan' => $kecepatan,
                'temperatur_udara' => $temperatur_udara,
            ),
            array('%d','%s', '%d', '%d', '%.2f')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/wilayah-kecamatan/";
        </script>';
        exit;
    }

    return '
        <form method="post">
        <label for="uraian">Bulan:</label>
        <input type="text" name="bulan" value="' . esc_attr($result->bulan) . '"><br>
        <label for="uraian">Arah Angin:</label>
        <input type="number" name="arah" value="' . esc_attr($result->arah) . '" inputmode="numeric" pattern="\d*"><br>
        <label for="uraian">Kecepatan:</label>
        <input type="number" name="kecepatan" value="' . esc_attr($result->kecepatan) . '" inputmode="numeric" pattern="\d*"><br>
        <label for="uraian">Temperatur Udara:</label>
        <input type="number" name="temperatur_udara" value="' . esc_attr($result->temperatur_udara) . '" step="0.01" inputmode="decimal" pattern="[0-9]+(\.[0-9]{1,2})?" /><br>
        <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_geografis_kecamatan_bintan_timur', 'tambah_data_geografis_kecamatan_bintan_timur');


function data_aparatur_kecamatan() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_aparatur_kecamatan';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
            text-align: left;
        }
        .custom-table th{
            font-size: 10px;
        }    
        .custom-table td{
            font-size: 10px;
        }  
        .custom-table th, .custom-table td {
            padding: 12px;
        }
        .custom-table thead {
            background-color: #f2f2f2;
            color: #333;
        }
        .custom-table tr {
            border-bottom: 1px solid #ddd;
        }
        /* Media Query untuk layar kecil */
        @media (max-width: 768px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font */
            }
        }
        @media (max-width: 480px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font lebih kecil */
            }
        }
    </style>';
    $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
    $output .= '<table class="custom-table">';
    $output .= '<thead>';
    $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Uraian</th>';
        $output .= '<th style="padding:12px;">Laki-Laki</th>';
        $output .= '<th style="padding:12px;">Perempuan</th>';
        $output .= '<th style="padding:12px;">Ket</th>';

        // Tambahkan kolom "Aksi" jika admin login
        if (current_user_can('administrator')) {
            $output .= '<th style="padding:12px;">Aksi</th>';
        }

        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->uraian) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->laki) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->perempuan) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->ket) . '</td>';

            // Jika admin login, tampilkan tombol Edit
            if (current_user_can('administrator')) {
                $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-data-aparatur-kecamatan-bintan-timur?id=' . $row->id . '">Edit</a></td>';

            }

            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }

    if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-data-aparatur-kecamatan" class="tambah-button">Tambah Data Aparatur Kecamatan</a>';
        $output .= '</div>';
    }
    
    return $output;
}

function edit_aparatur_data() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_aparatur_kecamatan'; // Nama tabel yang benar

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_aparatur_kecamatan',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/aparatur-pemerintah-kecamatan/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
            // Jika form disubmit untuk update
            if (isset($_POST['submit'])) {
                // Validasi dan sanitasi input dari form
                $uraian = sanitize_text_field($_POST['uraian']);
                $laki = intval($_POST['laki']);
                $perempuan = intval($_POST['perempuan']);
                $ket = sanitize_text_field($_POST['ket']);

                // Update data ke database
                $wpdb->update(
                    'data_aparatur_kecamatan',
                    array(
                        'uraian' => $uraian,
                        'laki' => $laki,
                        'perempuan' => $perempuan,
                        'ket' => $ket
                    ),
                    array('id' => $id),  // Syarat untuk update berdasarkan ID
                    array('%s', '%d', '%d', '%s'),  // Format tipe data
                    array('%d')  // Format untuk ID
                );

                // Redirect setelah data berhasil diupdate
                echo '<script type="text/javascript">
                    alert("Data berhasil diupdate.");
                    window.location.href = "http://websitekecamatanbintantimur.test/aparatur-pemerintah-kecamatan/";
                </script>';
                exit;
            }

            // Tampilkan form untuk mengedit data
            return '
                <form method="post">
                    <label for="uraian">Uraian:</label>
                    <input type="text" name="uraian" value="' . esc_attr($result->uraian) . '"><br>
                    <label for="laki">Laki-Laki:</label>
                    <input type="number" name="laki" value="' . esc_attr($result->laki) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="perempuan">Perempuan:</label>
                    <input type="number" name="perempuan" value="' . esc_attr($result->perempuan) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="ket">Keterangan:</label>
                    <input type="text" name="ket" value="' . esc_attr($result->ket) . '"><br>
                    <input type="submit" name="submit" value="Update Data" class="update-button">
                    <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
                </form>';
        } else {
            // Jika data tidak ditemukan
            return '<p>Data tidak ditemukan.</p>';
        }
    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_aparatur_data', 'edit_aparatur_data');


function tambah_data_aparatur_kecamatan() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_aparatur_kecamatan';

    if (isset($_POST['submit'])) {
        $uraian = sanitize_text_field($_POST['uraian']);
        $laki = intval($_POST['laki']);
        $perempuan = intval($_POST['perempuan']);
        $ket = sanitize_text_field($_POST['ket']);

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_aparatur_kecamatan',
            array(
                'nomor' => $new_nomor,
                'uraian' => $uraian,
                'laki' => $laki,
                'perempuan' => $perempuan,
                'ket' => $ket
            ),
            array('%d', '%s', '%d', '%d', '%s')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/aparatur-pemerintah-kecamatan/";
        </script>';
        exit;
    }

    return '
        <form method="post">
            <label for="uraian">Uraian:</label>
            <input type="text" name="uraian" required><br>
            <label for="laki">Laki-Laki:</label>
            <input type="number" name="laki" required><br>
            <label for="perempuan">Perempuan:</label>
            <input type="number" name="perempuan" required><br>
            <label for="ket">Keterangan:</label>
            <input type="text" name="ket" required><br>
            <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_aparatur_kecamatan', 'tambah_data_aparatur_kecamatan');

function  data_aparatur_keluruhan_kijang_kota() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_aparatur_keluruhan_kijang_kota';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
            text-align: left;
        }
        .custom-table th{
            font-size: 10px;
        }    
        .custom-table td{
            font-size: 10px;
        }  
        .custom-table th, .custom-table td {
            padding: 12px;
        }
        .custom-table thead {
            background-color: #f2f2f2;
            color: #333;
        }
        .custom-table tr {
            border-bottom: 1px solid #ddd;
        }
        /* Media Query untuk layar kecil */
        @media (max-width: 768px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font */
            }
        }
        @media (max-width: 480px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font lebih kecil */
            }
        }
        </style>';
        $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
        $output .= '<table class="custom-table">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Uraian</th>';
        $output .= '<th style="padding:12px;">Laki-Laki</th>';
        $output .= '<th style="padding:12px;">Perempuan</th>';
        $output .= '<th style="padding:12px;">Ket</th>';
                // Tambahkan kolom "Aksi" jika admin login
                if (current_user_can('administrator')) {
                    $output .= '<th style="padding:12px;">Aksi</th>';
                }
        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->uraian) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->laki) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->perempuan) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->ket) . '</td>';
             // Jika admin login, tampilkan tombol Edit
             if (current_user_can('administrator')) {
                $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-data-aparatur-kelurahan-kijang-kota?id=' . $row->id . '">Edit</a></td>';

            }
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }

    if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-data-aparatur-kelurahan-kijang-kota" class="tambah-button">Tambah Data Aparatur Kelurahan Kijang Kota</a>';
        $output .= '</div>';
    }

    return $output;
}

function edit_aparatur_kelurahan_kijang_kota() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_aparatur_keluruhan_kijang_kota'; // Nama tabel yang benar

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_aparatur_keluruhan_kijang_kota',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/aparatur-pemerintah-kecamatan/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
            // Jika form disubmit untuk update
            if (isset($_POST['submit'])) {
                // Validasi dan sanitasi input dari form
                $uraian = sanitize_text_field($_POST['uraian']);
                $laki = intval($_POST['laki']);
                $perempuan = intval($_POST['perempuan']);
                $ket = sanitize_text_field($_POST['ket']);

                // Update data ke database
                $wpdb->update(
                    'data_aparatur_keluruhan_kijang_kota',
                    array(
                        'uraian' => $uraian,
                        'laki' => $laki,
                        'perempuan' => $perempuan,
                        'ket' => $ket
                    ),
                    array('id' => $id),  // Syarat untuk update berdasarkan ID
                    array('%s', '%d', '%d', '%s'),  // Format tipe data
                    array('%d')  // Format untuk ID
                );

                // Redirect setelah data berhasil diupdate
                echo '<script type="text/javascript">
                    alert("Data berhasil diupdate.");
                    window.location.href = "http://websitekecamatanbintantimur.test/aparatur-pemerintah-kecamatan/";
                </script>';
                exit;
            }

            // Tampilkan form untuk mengedit data
            return '
                <form method="post">
                    <label for="uraian">Uraian:</label>
                    <input type="text" name="uraian" value="' . esc_attr($result->uraian) . '"><br>
                    <label for="laki">Laki-Laki:</label>
                    <input type="number" name="laki" value="' . esc_attr($result->laki) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="perempuan">Perempuan:</label>
                    <input type="number" name="perempuan" value="' . esc_attr($result->perempuan) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="ket">Keterangan:</label>
                    <input type="text" name="ket" value="' . esc_attr($result->ket) . '"><br>
                    <input type="submit" name="submit" value="Update Data" class="update-button">
                    <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
                </form>';
        } else {
            // Jika data tidak ditemukan
            return '<p>Data tidak ditemukan.</p>';
        }
    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_aparatur_kelurahan_kijang_kota', 'edit_aparatur_kelurahan_kijang_kota');


function tambah_data_aparatur_kelurahan_kijang_kota() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_aparatur_keluruhan_kijang_kota';

    if (isset($_POST['submit'])) {
        $uraian = sanitize_text_field($_POST['uraian']);
        $laki = intval($_POST['laki']);
        $perempuan = intval($_POST['perempuan']);
        $ket = sanitize_text_field($_POST['ket']);

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_aparatur_keluruhan_kijang_kota',
            array(
                'nomor' => $new_nomor,
                'uraian' => $uraian,
                'laki' => $laki,
                'perempuan' => $perempuan,
                'ket' => $ket
            ),
            array('%d', '%s', '%d', '%d', '%s')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/aparatur-pemerintah-kecamatan/";
        </script>';
        exit;
    }

    return '
        <form method="post">
            <label for="uraian">Uraian:</label>
            <input type="text" name="uraian" required><br>
            <label for="laki">Laki-Laki:</label>
            <input type="number" name="laki" required><br>
            <label for="perempuan">Perempuan:</label>
            <input type="number" name="perempuan" required><br>
            <label for="ket">Keterangan:</label>
            <input type="text" name="ket" required><br>
            <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_aparatur_kelurahan_kijang_kota', 'tambah_data_aparatur_kelurahan_kijang_kota');

function  data_aparatur_keluruhan_sungai_enam() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_aparatur_keluruhan_sungai_enam';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
            text-align: left;
        }
        .custom-table th{
            font-size: 10px;
        }    
        .custom-table td{
            font-size: 10px;
        }  
        .custom-table th, .custom-table td {
            padding: 12px;
        }
        .custom-table thead {
            background-color: #f2f2f2;
            color: #333;
        }
        .custom-table tr {
            border-bottom: 1px solid #ddd;
        }
        /* Media Query untuk layar kecil */
        @media (max-width: 768px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font */
            }
        }
        @media (max-width: 480px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font lebih kecil */
            }
        }
        </style>';

    $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
    $output .= '<table class="custom-table">';
    $output .= '<thead>';
    $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Uraian</th>';
        $output .= '<th style="padding:12px;">Laki-Laki</th>';
        $output .= '<th style="padding:12px;">Perempuan</th>';
        $output .= '<th style="padding:12px;">Ket</th>';
                // Tambahkan kolom "Aksi" jika admin login
                if (current_user_can('administrator')) {
                    $output .= '<th style="padding:12px;">Aksi</th>';
                }
        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->uraian) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->laki) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->perempuan) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->ket) . '</td>';

            // Jika admin login, tampilkan tombol Edit
            if (current_user_can('administrator')) {
                $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-data-aparatur-kelurahan-sungai-enam?id=' . $row->id . '">Edit</a></td>';

            }

            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }

    if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-data-aparatur-kelurahan-sungai-enam" class="tambah-button">Tambah Data Aparatur Kelurahan Sungai Enam</a>';
        $output .= '</div>';
    }

    return $output;
}

function edit_aparatur_keluruhan_sungai_enam() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_aparatur_keluruhan_sungai_enam'; // Nama tabel yang benar

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_aparatur_keluruhan_sungai_enam',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/aparatur-pemerintah-kecamatan/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
            // Jika form disubmit untuk update
            if (isset($_POST['submit'])) {
                // Validasi dan sanitasi input dari form
                $uraian = sanitize_text_field($_POST['uraian']);
                $laki = intval($_POST['laki']);
                $perempuan = intval($_POST['perempuan']);
                $ket = sanitize_text_field($_POST['ket']);

                // Update data ke database
                $wpdb->update(
                    'data_aparatur_keluruhan_sungai_enam',
                    array(
                        'uraian' => $uraian,
                        'laki' => $laki,
                        'perempuan' => $perempuan,
                        'ket' => $ket
                    ),
                    array('id' => $id),  // Syarat untuk update berdasarkan ID
                    array('%s', '%d', '%d', '%s'),  // Format tipe data
                    array('%d')  // Format untuk ID
                );

                // Redirect setelah data berhasil diupdate
                echo '<script type="text/javascript">
                    alert("Data berhasil diupdate.");
                    window.location.href = "http://websitekecamatanbintantimur.test/aparatur-pemerintah-kecamatan/";
                </script>';
                exit;
            }

            // Tampilkan form untuk mengedit data
            return '
                <form method="post">
                    <label for="uraian">Uraian:</label>
                    <input type="text" name="uraian" value="' . esc_attr($result->uraian) . '"><br>
                    <label for="laki">Laki-Laki:</label>
                    <input type="number" name="laki" value="' . esc_attr($result->laki) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="perempuan">Perempuan:</label>
                    <input type="number" name="perempuan" value="' . esc_attr($result->perempuan) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="ket">Keterangan:</label>
                    <input type="text" name="ket" value="' . esc_attr($result->ket) . '"><br>
                    <input type="submit" name="submit" value="Update Data" class="update-button">
                    <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
                </form>';
        } else {
            // Jika data tidak ditemukan
            return '<p>Data tidak ditemukan.</p>';
        }
    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_aparatur_keluruhan_sungai_enam', 'edit_aparatur_keluruhan_sungai_enam');


function tambah_data_aparatur_kelurahan_sungai_enam() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_aparatur_keluruhan_sungai_enam';

    if (isset($_POST['submit'])) {
        $uraian = sanitize_text_field($_POST['uraian']);
        $laki = intval($_POST['laki']);
        $perempuan = intval($_POST['perempuan']);
        $ket = sanitize_text_field($_POST['ket']);

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_aparatur_keluruhan_sungai_enam',
            array(
                'nomor' => $new_nomor,
                'uraian' => $uraian,
                'laki' => $laki,
                'perempuan' => $perempuan,
                'ket' => $ket
            ),
            array('%d', '%s', '%d', '%d', '%s')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/aparatur-pemerintah-kecamatan/";
        </script>';
        exit;
    }

    return '
        <form method="post">
            <label for="uraian">Uraian:</label>
            <input type="text" name="uraian" required><br>
            <label for="laki">Laki-Laki:</label>
            <input type="number" name="laki" required><br>
            <label for="perempuan">Perempuan:</label>
            <input type="number" name="perempuan" required><br>
            <label for="ket">Keterangan:</label>
            <input type="text" name="ket" required><br>
            <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_aparatur_kelurahan_sungai_enam', 'tambah_data_aparatur_kelurahan_sungai_enam');

function  data_aparatur_keluruhan_gunung_lengkuas() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_aparatur_keluruhan_gunung_lengkuas';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
            text-align: left;
        }
        .custom-table th{
            font-size: 10px;
        }    
        .custom-table td{
            font-size: 10px;
        }  
        .custom-table th, .custom-table td {
            padding: 12px;
        }
        .custom-table thead {
            background-color: #f2f2f2;
            color: #333;
        }
        .custom-table tr {
            border-bottom: 1px solid #ddd;
        }
        /* Media Query untuk layar kecil */
        @media (max-width: 768px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font */
            }
        }
        @media (max-width: 480px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font lebih kecil */
            }
        }
        </style>';
    $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
    $output .= '<table class="custom-table">';
    $output .= '<thead>';
    $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Uraian</th>';
        $output .= '<th style="padding:12px;">Laki-Laki</th>';
        $output .= '<th style="padding:12px;">Perempuan</th>';
        $output .= '<th style="padding:12px;">Ket</th>';

                // Tambahkan kolom "Aksi" jika admin login
                if (current_user_can('administrator')) {
                    $output .= '<th style="padding:12px;">Aksi</th>';
                }
        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->uraian) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->laki) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->perempuan) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->ket) . '</td>';

            // Jika admin login, tampilkan tombol Edit
            if (current_user_can('administrator')) {
                $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-data-aparatur-kelurahan-gunung-lengkuas?id=' . $row->id . '">Edit</a></td>';

            }
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }

    if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-data-aparatur-kelurahan-gunung-lengkuas" class="tambah-button">Tambah Data Aparatur Kelurahan Gunung Lengkuas</a>';
        $output .= '</div>';
    }

    return $output;
}

function edit_aparatur_keluruhan_gunung_lengkuas() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_aparatur_keluruhan_gunung_lengkuas'; // Nama tabel yang benar

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_aparatur_keluruhan_gunung_lengkuas',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/aparatur-pemerintah-kecamatan/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
            // Jika form disubmit untuk update
            if (isset($_POST['submit'])) {
                // Validasi dan sanitasi input dari form
                $uraian = sanitize_text_field($_POST['uraian']);
                $laki = intval($_POST['laki']);
                $perempuan = intval($_POST['perempuan']);
                $ket = sanitize_text_field($_POST['ket']);

                // Update data ke database
                $wpdb->update(
                    'data_aparatur_keluruhan_gunung_lengkuas',
                    array(
                        'uraian' => $uraian,
                        'laki' => $laki,
                        'perempuan' => $perempuan,
                        'ket' => $ket
                    ),
                    array('id' => $id),  // Syarat untuk update berdasarkan ID
                    array('%s', '%d', '%d', '%s'),  // Format tipe data
                    array('%d')  // Format untuk ID
                );

                // Redirect setelah data berhasil diupdate
                echo '<script type="text/javascript">
                    alert("Data berhasil diupdate.");
                    window.location.href = "http://websitekecamatanbintantimur.test/aparatur-pemerintah-kecamatan/";
                </script>';
                exit;
            }

            // Tampilkan form untuk mengedit data
            return '
                <form method="post">
                    <label for="uraian">Uraian:</label>
                    <input type="text" name="uraian" value="' . esc_attr($result->uraian) . '"><br>
                    <label for="laki">Laki-Laki:</label>
                    <input type="number" name="laki" value="' . esc_attr($result->laki) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="perempuan">Perempuan:</label>
                    <input type="number" name="perempuan" value="' . esc_attr($result->perempuan) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="ket">Keterangan:</label>
                    <input type="text" name="ket" value="' . esc_attr($result->ket) . '"><br>
                    <input type="submit" name="submit" value="Update Data" class="update-button">
                    <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
                </form>';
        } else {
            // Jika data tidak ditemukan
            return '<p>Data tidak ditemukan.</p>';
        }
    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_aparatur_keluruhan_gunung_lengkuas', 'edit_aparatur_keluruhan_gunung_lengkuas');


function tambah_data_aparatur_kelurahan_gunung_lengkuas() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_aparatur_keluruhan_gunung_lengkuas';

    if (isset($_POST['submit'])) {
        $uraian = sanitize_text_field($_POST['uraian']);
        $laki = intval($_POST['laki']);
        $perempuan = intval($_POST['perempuan']);
        $ket = sanitize_text_field($_POST['ket']);

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_aparatur_keluruhan_gunung_lengkuas',
            array(
                'nomor' => $new_nomor,
                'uraian' => $uraian,
                'laki' => $laki,
                'perempuan' => $perempuan,
                'ket' => $ket
            ),
            array('%d', '%s', '%d', '%d', '%s')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/aparatur-pemerintah-kecamatan/";
        </script>';
        exit;
    }

    return '
        <form method="post">
            <label for="uraian">Uraian:</label>
            <input type="text" name="uraian" required><br>
            <label for="laki">Laki-Laki:</label>
            <input type="number" name="laki" required><br>
            <label for="perempuan">Perempuan:</label>
            <input type="number" name="perempuan" required><br>
            <label for="ket">Keterangan:</label>
            <input type="text" name="ket" required><br>
            <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_aparatur_kelurahan_gunung_lengkuas', 'tambah_data_aparatur_kelurahan_gunung_lengkuas');


function  data_aparatur_keluruhan_sungai_lekop() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_aparatur_keluruhan_sungai_lekop';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
            text-align: left;
        }
        .custom-table th{
            font-size: 10px;
        }    
        .custom-table td{
            font-size: 10px;
        }  
        .custom-table th, .custom-table td {
            padding: 12px;
        }
        .custom-table thead {
            background-color: #f2f2f2;
            color: #333;
        }
        .custom-table tr {
            border-bottom: 1px solid #ddd;
        }
        /* Media Query untuk layar kecil */
        @media (max-width: 768px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font */
            }
        }
        @media (max-width: 480px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font lebih kecil */
            }
        }
        </style>';

        $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
        $output .= '<table class="custom-table">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Uraian</th>';
        $output .= '<th style="padding:12px;">Laki-Laki</th>';
        $output .= '<th style="padding:12px;">Perempuan</th>';
        $output .= '<th style="padding:12px;">Ket</th>';

        // Tambahkan kolom "Aksi" jika admin login
        if (current_user_can('administrator')) {
            $output .= '<th style="padding:12px;">Aksi</th>';
        }
        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->uraian) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->laki) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->perempuan) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->ket) . '</td>';

            // Jika admin login, tampilkan tombol Edit
            if (current_user_can('administrator')) {
                $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-data-aparatur-kelurahan-sungai-lekop?id=' . $row->id . '">Edit</a></td>';

            }
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }

    if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-data-aparatur-kelurahan-sungai-lekop" class="tambah-button">Tambah Data Aparatur Kelurahan Sungai Lekop</a>';
        $output .= '</div>';
    }

    return $output;
}

function edit_aparatur_keluruhan_sungai_lekop() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_aparatur_keluruhan_sungai_lekop'; // Nama tabel yang benar

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_aparatur_keluruhan_sungai_lekop',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/aparatur-pemerintah-kecamatan/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
            // Jika form disubmit untuk update
            if (isset($_POST['submit'])) {
                // Validasi dan sanitasi input dari form
                $uraian = sanitize_text_field($_POST['uraian']);
                $laki = intval($_POST['laki']);
                $perempuan = intval($_POST['perempuan']);
                $ket = sanitize_text_field($_POST['ket']);

                // Update data ke database
                $wpdb->update(
                    'data_aparatur_keluruhan_sungai_lekop',
                    array(
                        'uraian' => $uraian,
                        'laki' => $laki,
                        'perempuan' => $perempuan,
                        'ket' => $ket
                    ),
                    array('id' => $id),  // Syarat untuk update berdasarkan ID
                    array('%s', '%d', '%d', '%s'),  // Format tipe data
                    array('%d')  // Format untuk ID
                );

                // Redirect setelah data berhasil diupdate
                echo '<script type="text/javascript">
                    alert("Data berhasil diupdate.");
                    window.location.href = "http://websitekecamatanbintantimur.test/aparatur-pemerintah-kecamatan/";
                </script>';
                exit;
            }

            // Tampilkan form untuk mengedit data
            return '
                <form method="post">
                    <label for="uraian">Uraian:</label>
                    <input type="text" name="uraian" value="' . esc_attr($result->uraian) . '"><br>
                    <label for="laki">Laki-Laki:</label>
                    <input type="number" name="laki" value="' . esc_attr($result->laki) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="perempuan">Perempuan:</label>
                    <input type="number" name="perempuan" value="' . esc_attr($result->perempuan) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="ket">Keterangan:</label>
                    <input type="text" name="ket" value="' . esc_attr($result->ket) . '"><br>
                    <input type="submit" name="submit" value="Update Data" class="update-button">
                    <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
                </form>';
        } else {
            // Jika data tidak ditemukan
            return '<p>Data tidak ditemukan.</p>';
        }
    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_aparatur_keluruhan_sungai_lekop', 'edit_aparatur_keluruhan_sungai_lekop');


function tambah_data_aparatur_kelurahan_sungai_lekop() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_aparatur_keluruhan_sungai_lekop';

    if (isset($_POST['submit'])) {
        $uraian = sanitize_text_field($_POST['uraian']);
        $laki = intval($_POST['laki']);
        $perempuan = intval($_POST['perempuan']);
        $ket = sanitize_text_field($_POST['ket']);

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_aparatur_keluruhan_sungai_lekop',
            array(
                'nomor' => $new_nomor,
                'uraian' => $uraian,
                'laki' => $laki,
                'perempuan' => $perempuan,
                'ket' => $ket
            ),
            array('%d', '%s', '%d', '%d', '%s')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/aparatur-pemerintah-kecamatan/";
        </script>';
        exit;
    }

    return '
        <form method="post">
            <label for="uraian">Uraian:</label>
            <input type="text" name="uraian" required><br>
            <label for="laki">Laki-Laki:</label>
            <input type="number" name="laki" required><br>
            <label for="perempuan">Perempuan:</label>
            <input type="number" name="perempuan" required><br>
            <label for="ket">Keterangan:</label>
            <input type="text" name="ket" required><br>
            <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_aparatur_kelurahan_sungai_lekop', 'tambah_data_aparatur_kelurahan_sungai_lekop');

function data_kepengurusan_pkk_kecamatan() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_kepengurusan_pkk_kecamatan';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
            text-align: left;
        }
        .custom-table th{
            font-size: 10px;
        }    
        .custom-table td{
            font-size: 10px;
        }  
        .custom-table th, .custom-table td {
            padding: 12px;
        }
        .custom-table thead {
            background-color: #f2f2f2;
            color: #333;
        }
        .custom-table tr {
            border-bottom: 1px solid #ddd;
        }
        /* Media Query untuk layar kecil */
        @media (max-width: 768px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font */
            }
        }
        @media (max-width: 480px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font lebih kecil */
            }
        }
        </style>';

        $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
        $output .= '<table class="custom-table">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Nama</th>';
        $output .= '<th style="padding:12px;">Jabatan</th>';
        $output .= '<th style="padding:12px;">Ket</th>';
                        // Tambahkan kolom "Aksi" jika admin login
                        if (current_user_can('administrator')) {
                            $output .= '<th style="padding:12px;">Aksi</th>';
                        }
        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            $output .= '<td style="padding:12px;">' . format_text(esc_html($row->nama)) . '</td>';
            $output .= '<td style="padding:12px;">' . format_text(esc_html($row->jabatan)) . '</td>';
            $output .= '<td style="padding:12px;">' . format_text(esc_html($row->ket)) . '</td>';
            // Jika admin login, tampilkan tombol Edit
            if (current_user_can('administrator')) {
                $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-data-susunan-kepengurusan-tim-penggerak-pkk-kecamatan?id=' . $row->id . '">Edit</a></td>';

            }
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }

    if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-susunan-kepengurusan-tim-penggerak-pkk-kecamatan" class="tambah-button">Tambah Data Susunan Kepengurusan Tim Penggerak PKK Kecamatan</a>';
        $output .= '</div>';
    }

    return $output;
}

function edit_kepengurusan_pkk_kecamatan() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_kepengurusan_pkk_kecamatan';

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_kepengurusan_pkk_kecamatan',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/lembaga-organisasi-dan-partai-politik/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
            // Jika form disubmit untuk update
            if (isset($_POST['submit'])) {
                // Validasi dan sanitasi input dari form
                $nama = sanitize_text_field($_POST['nama']);
                $jabatan = sanitize_text_field($_POST['jabatan']);
                $ket = sanitize_text_field($_POST['ket']);

                // Update data ke database
                $wpdb->update(
                    'data_kepengurusan_pkk_kecamatan',
                    array(
                        'nama' => $nama,
                        'jabatan' => $jabatan,
                        'ket' => $ket
                    ),
                    array('id' => $id),  // Syarat untuk update berdasarkan ID
                    array('%s', '%s', '%s'),  // Format tipe data
                    array('%d')  // Format untuk ID
                );

                // Redirect setelah data berhasil diupdate
                echo '<script type="text/javascript">
                    alert("Data berhasil diupdate.");
                    window.location.href = "http://websitekecamatanbintantimur.test/lembaga-organisasi-dan-partai-politik/";
                </script>';
                exit;
            }

            // Tampilkan form untuk mengedit data
            return '
                <form method="post">
                    <label for="uraian">Nama:</label>
                    <input type="text" name="nama" value="' . esc_attr($result->nama) . '"><br>
                    <label for="uraian">Jabatan:</label>
                    <input type="text" name="jabatan" value="' . esc_attr($result->jabatan) . '"><br>
                    <label for="ket">Keterangan:</label>
                    <input type="text" name="ket" value="' . esc_attr($result->ket) . '"><br>
                    <input type="submit" name="submit" value="Update Data" class="update-button">
                    <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
                </form>';
        } else {
            // Jika data tidak ditemukan
            return '<p>Data tidak ditemukan.</p>';
        }
    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_kepengurusan_pkk_kecamatan', 'edit_kepengurusan_pkk_kecamatan');


function tambah_data_kepengurusan_pkk_kecamatan() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_kepengurusan_pkk_kecamatan';

    if (isset($_POST['submit'])) {
        $nama = sanitize_text_field($_POST['nama']);
        $jabatan = sanitize_text_field($_POST['jabatan']);
        $ket = sanitize_text_field($_POST['ket']);

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_kepengurusan_pkk_kecamatan',
            array(
                'nomor' => $new_nomor,
                'nama' => $nama,
                'jabatan' => $jabatan,
                'ket' => $ket
            ),
            array('%d', '%s', '%s', '%s')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/lembaga-organisasi-dan-partai-politik/";
        </script>';
        exit;
    }

    return '
        <form method="post">
        <label for="uraian">Nama:</label>
        <input type="text" name="nama" value="' . esc_attr($result->nama) . '"><br>
        <label for="uraian">Jabatan:</label>
        <input type="text" name="jabatan" value="' . esc_attr($result->jabatan) . '"><br>
        <label for="ket">Keterangan:</label>
        <input type="text" name="ket" value="' . esc_attr($result->ket) . '"><br>
        <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_kepengurusan_pkk_kecamatan', 'tambah_data_kepengurusan_pkk_kecamatan');

function data_pokja_1() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_pokja_1';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
            text-align: left;
        }
        .custom-table th{
            font-size: 10px;
        }    
        .custom-table td{
            font-size: 10px;
        }  
        .custom-table th, .custom-table td {
            padding: 12px;
        }
        .custom-table thead {
            background-color: #f2f2f2;
            color: #333;
        }
        .custom-table tr {
            border-bottom: 1px solid #ddd;
        }
        /* Media Query untuk layar kecil */
        @media (max-width: 768px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font */
            }
        }
        @media (max-width: 480px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font lebih kecil */
            }
        }
        </style>';

        $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
        $output .= '<table class="custom-table">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Kelompok Kerja 1 (POKJA 1)</th>';
        $output .= '<th style="padding:12px;">Jabatan</th>';
                        // Tambahkan kolom "Aksi" jika admin login
                        if (current_user_can('administrator')) {
                            $output .= '<th style="padding:12px;">Aksi</th>';
                        }
        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            $output .= '<td style="padding:12px;">' . format_text(esc_html($row->kelompok_kerja)) . '</td>';
            $output .= '<td style="padding:12px;">' . format_text(esc_html($row->jabatan)) . '</td>';
            // Jika admin login, tampilkan tombol Edit
            if (current_user_can('administrator')) {
                $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-kepengurusan-kelompok-kerja-satu?id=' . $row->id . '">Edit</a></td>';

            }
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }

    if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-data-kelompok-kerja-satu" class="tambah-button">Tambah Data Kelompok Kerja POKJA Satu</a>';
        $output .= '</div>';
    }

    return $output;
}

function edit_kepengurusan_kelompok_kerja_satu() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_pokja_1'; // Nama tabel yang benar

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_pokja_1',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/lembaga-organisasi-dan-partai-politik/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
            // Jika form disubmit untuk update
            if (isset($_POST['submit'])) {
                // Validasi dan sanitasi input dari form
                $kelompok_kerja = sanitize_text_field($_POST['kelompok_kerja']);
                $jabatan = sanitize_text_field($_POST['jabatan']);

                // Update data ke database
                $wpdb->update(
                    'data_pokja_1',
                    array(
                        'kelompok_kerja' => $kelompok_kerja,
                        'jabatan' => $jabatan,
                    ),
                    array('id' => $id),  // Syarat untuk update berdasarkan ID
                    array('%s', '%s'),  // Format tipe data
                    array('%d')  // Format untuk ID
                );

                // Redirect setelah data berhasil diupdate
                echo '<script type="text/javascript">
                    alert("Data berhasil diupdate.");
                    window.location.href = "http://websitekecamatanbintantimur.test/lembaga-organisasi-dan-partai-politik/";
                </script>';
                exit;
            }

            // Tampilkan form untuk mengedit data
            return '
                <form method="post">
                    <label for="uraian">Nama Anggota POKJA Satu:</label>
                    <input type="text" name="kelompok_kerja" value="' . esc_attr($result->kelompok_kerja) . '"><br>
                    <label for="uraian">Jabatan:</label>
                    <input type="text" name="jabatan" value="' . esc_attr($result->jabatan) . '"><br>
                    <input type="submit" name="submit" value="Update Data" class="update-button">
                    <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
                </form>';
        } else {
            // Jika data tidak ditemukan
            return '<p>Data tidak ditemukan.</p>';
        }
    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_kepengurusan_kelompok_kerja_satu', 'edit_kepengurusan_kelompok_kerja_satu');


function tambah_data_kelompok_kerja_satu() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_pokja_1';

    if (isset($_POST['submit'])) {
        $kelompok_kerja = sanitize_text_field($_POST['kelompok_kerja']);
        $jabatan = sanitize_text_field($_POST['jabatan']);

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_pokja_1',
            array(
                'nomor' => $new_nomor,
                'kelompok_kerja' => $kelompok_kerja,
                'jabatan' => $jabatan,
            ),
            array('%d', '%s', '%s')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/lembaga-organisasi-dan-partai-politik/";
        </script>';
        exit;
    }

    return '
        <form method="post">
        <label for="uraian">Nama Anggota POKJA Satu:</label>
        <input type="text" name="kelompok_kerja" value="' . esc_attr($result->kelompok_kerja) . '"><br>
        <label for="uraian">Jabatan:</label>
        <input type="text" name="jabatan" value="' . esc_attr($result->jabatan) . '"><br>
        <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_kelompok_kerja_satu', 'tambah_data_kelompok_kerja_satu');

function data_pokja_2() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_pokja_2';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
            text-align: left;
        }
        .custom-table th{
            font-size: 10px;
        }    
        .custom-table td{
            font-size: 10px;
        }  
        .custom-table th, .custom-table td {
            padding: 12px;
        }
        .custom-table thead {
            background-color: #f2f2f2;
            color: #333;
        }
        .custom-table tr {
            border-bottom: 1px solid #ddd;
        }
        /* Media Query untuk layar kecil */
        @media (max-width: 768px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font */
            }
        }
        @media (max-width: 480px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font lebih kecil */
            }
        }
        </style>';

        $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
        $output .= '<table class="custom-table">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Kelompok Kerja 2 (POKJA 2)</th>';
        $output .= '<th style="padding:12px;">Jabatan</th>';
                        // Tambahkan kolom "Aksi" jika admin login
                        if (current_user_can('administrator')) {
                            $output .= '<th style="padding:12px;">Aksi</th>';
                        }
        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            $output .= '<td style="padding:12px;">' . format_text(esc_html($row->kelompok_kerja)) . '</td>';
            $output .= '<td style="padding:12px;">' . format_text(esc_html($row->jabatan)) . '</td>';
            // Jika admin login, tampilkan tombol Edit
            if (current_user_can('administrator')) {
                $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-kepengurusan-kelompok-kerja-dua?id=' . $row->id . '">Edit</a></td>';

            }
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }

    if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-data-kelompok-kerja-dua" class="tambah-button">Tambah Data Kelompok Kerja POKJA Dua</a>';
        $output .= '</div>';
    }

    return $output;
}

function edit_kepengurusan_kelompok_kerja_dua() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_pokja_2'; // Nama tabel yang benar

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_pokja_2',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/lembaga-organisasi-dan-partai-politik/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
            // Jika form disubmit untuk update
            if (isset($_POST['submit'])) {
                // Validasi dan sanitasi input dari form
                $kelompok_kerja = sanitize_text_field($_POST['kelompok_kerja']);
                $jabatan = sanitize_text_field($_POST['jabatan']);

                // Update data ke database
                $wpdb->update(
                    'data_pokja_2',
                    array(
                        'kelompok_kerja' => $kelompok_kerja,
                        'jabatan' => $jabatan,
                    ),
                    array('id' => $id),  // Syarat untuk update berdasarkan ID
                    array('%s', '%s'),  // Format tipe data
                    array('%d')  // Format untuk ID
                );

                // Redirect setelah data berhasil diupdate
                echo '<script type="text/javascript">
                    alert("Data berhasil diupdate.");
                    window.location.href = "http://websitekecamatanbintantimur.test/lembaga-organisasi-dan-partai-politik/";
                </script>';
                exit;
            }

            // Tampilkan form untuk mengedit data
            return '
                <form method="post">
                    <label for="uraian">Nama Anggota POKJA Dua:</label>
                    <input type="text" name="kelompok_kerja" value="' . esc_attr($result->kelompok_kerja) . '"><br>
                    <label for="uraian">Jabatan:</label>
                    <input type="text" name="jabatan" value="' . esc_attr($result->jabatan) . '"><br>
                    <input type="submit" name="submit" value="Update Data" class="update-button">
                    <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
                </form>';
        } else {
            // Jika data tidak ditemukan
            return '<p>Data tidak ditemukan.</p>';
        }
    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_kepengurusan_kelompok_kerja_dua', 'edit_kepengurusan_kelompok_kerja_dua');


function tambah_data_kelompok_kerja_dua() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_pokja_2';

    if (isset($_POST['submit'])) {
        $kelompok_kerja = sanitize_text_field($_POST['kelompok_kerja']);
        $jabatan = sanitize_text_field($_POST['jabatan']);

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_pokja_2',
            array(
                'nomor' => $new_nomor,
                'kelompok_kerja' => $kelompok_kerja,
                'jabatan' => $jabatan,
            ),
            array('%d', '%s', '%s')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/lembaga-organisasi-dan-partai-politik/";
        </script>';
        exit;
    }

    return '
        <form method="post">
        <label for="uraian">Nama Anggota POKJA Dua:</label>
        <input type="text" name="kelompok_kerja" value="' . esc_attr($result->kelompok_kerja) . '"><br>
        <label for="uraian">Jabatan:</label>
        <input type="text" name="jabatan" value="' . esc_attr($result->jabatan) . '"><br>
        <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_kelompok_kerja_dua', 'tambah_data_kelompok_kerja_dua');

function data_pokja_3() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_pokja_3';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
            text-align: left;
        }
        .custom-table th{
            font-size: 10px;
        }    
        .custom-table td{
            font-size: 10px;
        }  
        .custom-table th, .custom-table td {
            padding: 12px;
        }
        .custom-table thead {
            background-color: #f2f2f2;
            color: #333;
        }
        .custom-table tr {
            border-bottom: 1px solid #ddd;
        }
        /* Media Query untuk layar kecil */
        @media (max-width: 768px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font */
            }
        }
        @media (max-width: 480px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font lebih kecil */
            }
        }
        </style>';

        $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
        $output .= '<table class="custom-table">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Kelompok Kerja 3 (POKJA 3)</th>';
        $output .= '<th style="padding:12px;">Jabatan</th>';
                        // Tambahkan kolom "Aksi" jika admin login
                        if (current_user_can('administrator')) {
                            $output .= '<th style="padding:12px;">Aksi</th>';
                        }
        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            $output .= '<td style="padding:12px;">' . format_text(esc_html($row->kelompok_kerja)) . '</td>';
            $output .= '<td style="padding:12px;">' . format_text(esc_html($row->jabatan)) . '</td>';
            // Jika admin login, tampilkan tombol Edit
            if (current_user_can('administrator')) {
                $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-kepengurusan-kelompok-kerja-tiga?id=' . $row->id . '">Edit</a></td>';

            }
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }

    if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-data-kelompok-kerja-tiga" class="tambah-button">Tambah Data Kelompok Kerja POKJA Tiga</a>';
        $output .= '</div>';
    }

    return $output;
}

function edit_kepengurusan_kelompok_kerja_tiga() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_pokja_3'; // Nama tabel yang benar

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_pokja_3',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/lembaga-organisasi-dan-partai-politik/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
            // Jika form disubmit untuk update
            if (isset($_POST['submit'])) {
                // Validasi dan sanitasi input dari form
                $kelompok_kerja = sanitize_text_field($_POST['kelompok_kerja']);
                $jabatan = sanitize_text_field($_POST['jabatan']);

                // Update data ke database
                $wpdb->update(
                    'data_pokja_3',
                    array(
                        'kelompok_kerja' => $kelompok_kerja,
                        'jabatan' => $jabatan,
                    ),
                    array('id' => $id),  // Syarat untuk update berdasarkan ID
                    array('%s', '%s'),  // Format tipe data
                    array('%d')  // Format untuk ID
                );

                // Redirect setelah data berhasil diupdate
                echo '<script type="text/javascript">
                    alert("Data berhasil diupdate.");
                    window.location.href = "http://websitekecamatanbintantimur.test/lembaga-organisasi-dan-partai-politik/";
                </script>';
                exit;
            }

            // Tampilkan form untuk mengedit data
            return '
                <form method="post">
                    <label for="uraian">Nama Anggota POKJA Tiga:</label>
                    <input type="text" name="kelompok_kerja" value="' . esc_attr($result->kelompok_kerja) . '"><br>
                    <label for="uraian">Jabatan:</label>
                    <input type="text" name="jabatan" value="' . esc_attr($result->jabatan) . '"><br>
                    <input type="submit" name="submit" value="Update Data" class="update-button">
                    <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
                </form>';
        } else {
            // Jika data tidak ditemukan
            return '<p>Data tidak ditemukan.</p>';
        }
    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_kepengurusan_kelompok_kerja_tiga', 'edit_kepengurusan_kelompok_kerja_tiga');


function tambah_data_kelompok_kerja_tiga() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_pokja_3';

    if (isset($_POST['submit'])) {
        $kelompok_kerja = sanitize_text_field($_POST['kelompok_kerja']);
        $jabatan = sanitize_text_field($_POST['jabatan']);

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_pokja_3',
            array(
                'nomor' => $new_nomor,
                'kelompok_kerja' => $kelompok_kerja,
                'jabatan' => $jabatan,
            ),
            array('%d', '%s', '%s')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/lembaga-organisasi-dan-partai-politik/";
        </script>';
        exit;
    }

    return '
        <form method="post">
        <label for="uraian">Nama Anggota POKJA Tiga:</label>
        <input type="text" name="kelompok_kerja" value="' . esc_attr($result->kelompok_kerja) . '"><br>
        <label for="uraian">Jabatan:</label>
        <input type="text" name="jabatan" value="' . esc_attr($result->jabatan) . '"><br>
        <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_kelompok_kerja_tiga', 'tambah_data_kelompok_kerja_tiga');

function data_pokja_4() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_pokja_4';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
            text-align: left;
        }
        .custom-table th{
            font-size: 10px;
        }    
        .custom-table td{
            font-size: 10px;
        }  
        .custom-table th, .custom-table td {
            padding: 12px;
        }
        .custom-table thead {
            background-color: #f2f2f2;
            color: #333;
        }
        .custom-table tr {
            border-bottom: 1px solid #ddd;
        }
        /* Media Query untuk layar kecil */
        @media (max-width: 768px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font */
            }
        }
        @media (max-width: 480px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font lebih kecil */
            }
        }
        </style>';

        $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
        $output .= '<table class="custom-table">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Kelompok Kerja 4 (POKJA 4)</th>';
        $output .= '<th style="padding:12px;">Jabatan</th>';
                        // Tambahkan kolom "Aksi" jika admin login
                        if (current_user_can('administrator')) {
                            $output .= '<th style="padding:12px;">Aksi</th>';
                        }
        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            $output .= '<td style="padding:12px;">' . format_text(esc_html($row->kelompok_kerja)) . '</td>';
            $output .= '<td style="padding:12px;">' . format_text(esc_html($row->jabatan)) . '</td>';
            // Jika admin login, tampilkan tombol Edit
            if (current_user_can('administrator')) {
                $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-kepengurusan-kelompok-kerja-empat?id=' . $row->id . '">Edit</a></td>';

            }
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }

    if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-data-kelompok-kerja-empat" class="tambah-button">Tambah Data Kelompok Kerja POKJA Empat</a>';
        $output .= '</div>';
    }

    return $output;
}

function edit_kepengurusan_kelompok_kerja_empat() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_pokja_4'; // Nama tabel yang benar

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_pokja_4',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/lembaga-organisasi-dan-partai-politik/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
            // Jika form disubmit untuk update
            if (isset($_POST['submit'])) {
                // Validasi dan sanitasi input dari form
                $kelompok_kerja = sanitize_text_field($_POST['kelompok_kerja']);
                $jabatan = sanitize_text_field($_POST['jabatan']);

                // Update data ke database
                $wpdb->update(
                    'data_pokja_4',
                    array(
                        'kelompok_kerja' => $kelompok_kerja,
                        'jabatan' => $jabatan,
                    ),
                    array('id' => $id),  // Syarat untuk update berdasarkan ID
                    array('%s', '%s'),  // Format tipe data
                    array('%d')  // Format untuk ID
                );

                // Redirect setelah data berhasil diupdate
                echo '<script type="text/javascript">
                    alert("Data berhasil diupdate.");
                    window.location.href = "http://websitekecamatanbintantimur.test/lembaga-organisasi-dan-partai-politik/";
                </script>';
                exit;
            }

            // Tampilkan form untuk mengedit data
            return '
                <form method="post">
                    <label for="uraian">Nama Anggota POKJA Empat:</label>
                    <input type="text" name="kelompok_kerja" value="' . esc_attr($result->kelompok_kerja) . '"><br>
                    <label for="uraian">Jabatan:</label>
                    <input type="text" name="jabatan" value="' . esc_attr($result->jabatan) . '"><br>
                    <input type="submit" name="submit" value="Update Data" class="update-button">
                    <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
                </form>';
        } else {
            // Jika data tidak ditemukan
            return '<p>Data tidak ditemukan.</p>';
        }
    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_kepengurusan_kelompok_kerja_empat', 'edit_kepengurusan_kelompok_kerja_empat');


function tambah_data_kelompok_kerja_empat() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_pokja_4';

    if (isset($_POST['submit'])) {
        $kelompok_kerja = sanitize_text_field($_POST['kelompok_kerja']);
        $jabatan = sanitize_text_field($_POST['jabatan']);

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_pokja_4',
            array(
                'nomor' => $new_nomor,
                'kelompok_kerja' => $kelompok_kerja,
                'jabatan' => $jabatan,
            ),
            array('%d', '%s', '%s')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/lembaga-organisasi-dan-partai-politik/";
        </script>';
        exit;
    }

    return '
        <form method="post">
        <label for="uraian">Nama Anggota POKJA Empat:</label>
        <input type="text" name="kelompok_kerja" value="' . esc_attr($result->kelompok_kerja) . '"><br>
        <label for="uraian">Jabatan:</label>
        <input type="text" name="jabatan" value="' . esc_attr($result->jabatan) . '"><br>
        <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_kelompok_kerja_empat', 'tambah_data_kelompok_kerja_empat');


function  data_lembaga_organisasi_kemasyarakatan_rt_rw() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_lembaga_organisasi_kemasyarakatan_rt_rw';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
            text-align: left;
        }
        .custom-table th{
            font-size: 10px;
        }    
        .custom-table td{
            font-size: 10px;
        }  
        .custom-table th, .custom-table td {
            padding: 12px;
        }
        .custom-table thead {
            background-color: #f2f2f2;
            color: #333;
        }
        .custom-table tr {
            border-bottom: 1px solid #ddd;
        }
        /* Media Query untuk layar kecil */
        @media (max-width: 768px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font */
            }
        }
        @media (max-width: 480px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font lebih kecil */
            }
        }
    </style>';

        $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
        $output .= '<table class="custom-table">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Kelurahan</th>';
        $output .= '<th style="padding:12px;">RT</th>';
        $output .= '<th style="padding:12px;">RW</th>';
        $output .= '<th style="padding:12px;">Jumlah</th>';
                         // Tambahkan kolom "Aksi" jika admin login
                        if (current_user_can('administrator')) {
                            $output .= '<th style="padding:12px;">Aksi</th>';
                        }
        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->kelurahan) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->rt) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->rw) . '</td>';

            $jumlah = $row->rt + $row->rw;
            $output .= '<td style="padding:12px;">' . esc_html($jumlah) . '</td>';
                        // Jika admin login, tampilkan tombol Edit
                        if (current_user_can('administrator')) {
                            $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-data-lembaga-organisasi-kemasyarakatan-rt-rw?id=' . $row->id . '">Edit</a></td>';
            
                        }
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }

    if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-data-lembaga-organisasi-kemasyarakatan-rt-rw" class="tambah-button">Tambah Data Lembaga Organisasi Kemasyarakatan (RT/RW)</a>';
        $output .= '</div>';
    }

    return $output;
}

function edit_data_lembaga_organisasi_kemasyarakatan_rt_rw() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_lembaga_organisasi_kemasyarakatan_rt_rw'; // Nama tabel yang benar

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_lembaga_organisasi_kemasyarakatan_rt_rw',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/lembaga-organisasi-dan-partai-politik/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
            // Jika form disubmit untuk update
            if (isset($_POST['submit'])) {
                // Validasi dan sanitasi input dari form
                $kelurahan = sanitize_text_field($_POST['kelurahan']);
                $rt = intval($_POST['rt']);
                $rw = intval($_POST['rw']);
                $jumlah = intval($_POST['jumlah']);

                // Update data ke database
                $wpdb->update(
                    'data_lembaga_organisasi_kemasyarakatan_rt_rw',
                    array(
                        'kelurahan' => $kelurahan,
                        'rt' => $rt,
                        'rw' => $rw,
                        'jumlah' => $jumlah,
                    ),
                    array('id' => $id),  // Syarat untuk update berdasarkan ID
                    array('%s', '%d', '%d', '%d'),  // Format tipe data
                    array('%d')  // Format untuk ID
                );

                // Redirect setelah data berhasil diupdate
                echo '<script type="text/javascript">
                    alert("Data berhasil diupdate.");
                    window.location.href = "http://websitekecamatanbintantimur.test/lembaga-organisasi-dan-partai-politik/";
                </script>';
                exit;
            }

            // Tampilkan form untuk mengedit data
            return '
                <form method="post">
                    <label for="uraian">Nama Kelurahan:</label>
                    <input type="text" name="kelurahan" value="' . esc_attr($result->kelurahan) . '"><br>
                    <label for="uraian">RT:</label>
                    <input type="number" name="rt" value="' . esc_attr($result->rt) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">RW:</label>
                    <input type="number" name="rw" value="' . esc_attr($result->rw) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah:</label>
                    <input type="number" name="jumlah" value="' . esc_attr($result->jumlah) . '" inputmode="numeric" pattern="\d*"><br>
                    <input type="submit" name="submit" value="Update Data" class="update-button">
                    <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
                </form>';
        } else {
            // Jika data tidak ditemukan
            return '<p>Data tidak ditemukan.</p>';
        }
    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_data_lembaga_organisasi_kemasyarakatan_rt_rw', 'edit_data_lembaga_organisasi_kemasyarakatan_rt_rw');


function tambah_data_lembaga_organisasi_kemasyarakatan_rt_rw() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_lembaga_organisasi_kemasyarakatan_rt_rw';

    if (isset($_POST['submit'])) {
        $kelurahan = sanitize_text_field($_POST['kelurahan']);
        $rt = intval($_POST['rt']);
        $rw = intval($_POST['rw']);
        $jumlah = intval($_POST['jumlah']);

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_lembaga_organisasi_kemasyarakatan_rt_rw',
            array(
                'nomor' => $new_nomor,
                'kelurahan' => $kelurahan,
                'rt' => $rt,
                'rw' => $rw,
                'jumlah' => $jumlah,
            ),
            array('%d','%s', '%d', '%d', '%d')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/lembaga-organisasi-dan-partai-politik/";
        </script>';
        exit;
    }

    return '
        <form method="post">
        <label for="uraian">Nama Kelurahan:</label>
        <input type="text" name="kelurahan" value="' . esc_attr($result->kelurahan) . '"><br>
        <label for="uraian">RT:</label>
        <input type="number" name="rt" value="' . esc_attr($result->rt) . '" inputmode="numeric" pattern="\d*"><br>
        <label for="uraian">RW:</label>
        <input type="number" name="rw" value="' . esc_attr($result->rw) . '" inputmode="numeric" pattern="\d*"><br>
        <label for="uraian">Jumlah:</label>
        <input type="number" name="jumlah" value="' . esc_attr($result->jumlah) . '" inputmode="numeric" pattern="\d*"><br>
        <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_lembaga_organisasi_kemasyarakatan_rt_rw', 'tambah_data_lembaga_organisasi_kemasyarakatan_rt_rw');

function data_lpm_kelurahan_kijang_kota() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_lpm_kelurahan_kijang_kota';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
            text-align: left;
        }
        .custom-table th{
            font-size: 10px;
        }    
        .custom-table td{
            font-size: 10px;
        }  
        .custom-table th, .custom-table td {
            padding: 12px;
        }
        .custom-table thead {
            background-color: #f2f2f2;
            color: #333;
        }
        .custom-table tr {
            border-bottom: 1px solid #ddd;
        }
        /* Media Query untuk layar kecil */
        @media (max-width: 768px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font */
            }
        }
        @media (max-width: 480px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font lebih kecil */
            }
        }
        </style>';

        $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
        $output .= '<table class="custom-table">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Kelurahan</th>';
        $output .= '<th style="padding:12px;">Nama</th>';
        $output .= '<th style="padding:12px;">Jabatan</th>';
        $output .= '<th style="padding:12px;">Ket</th>';
                        // Tambahkan kolom "Aksi" jika admin login
                        if (current_user_can('administrator')) {
                            $output .= '<th style="padding:12px;">Aksi</th>';
                        }
        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            $output .= '<td style="padding:12px;">' . format_text(esc_html($row->kelurahan)) . '</td>';
            $output .= '<td style="padding:12px;">' . format_text(esc_html($row->nama)) . '</td>';
            $output .= '<td style="padding:12px;">' . format_text(esc_html($row->jabatan)) . '</td>';
            $output .= '<td style="padding:12px;">' . format_text(esc_html($row->ket)) . '</td>';
            // Jika admin login, tampilkan tombol Edit
            if (current_user_can('administrator')) {
                $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-data-lembaga-pemberdayaan-masyarakat-kelurahan-kijang-kota?id=' . $row->id . '">Edit</a></td>';

            }
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }

    if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-data-lembaga-pemberdayaan-masyarakat-kelurahan-kijang-kota" class="tambah-button">Tambah Data (LPM) Kelurahan Kijang Kota</a>';
        $output .= '</div>';
    }

    return $output;
}

function edit_data_lpm_kelurahan_kijang_kota() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_lpm_kelurahan_kijang_kota'; // Nama tabel yang benar

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_lpm_kelurahan_kijang_kota',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/lembaga-organisasi-dan-partai-politik/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
            // Jika form disubmit untuk update
            if (isset($_POST['submit'])) {
                // Validasi dan sanitasi input dari form
                $kelurahan = sanitize_text_field($_POST['kelurahan']);
                $nama = sanitize_text_field($_POST['nama']);
                $jabatan = sanitize_text_field($_POST['jabatan']);
                $ket = sanitize_text_field($_POST['ket']);

                // Update data ke database
                $wpdb->update(
                    'data_lpm_kelurahan_kijang_kota',
                    array(
                        'kelurahan' => $kelurahan,
                        'nama' => $nama,
                        'jabatan' => $jabatan,
                        'ket' => $ket,
                    ),
                    array('id' => $id),  // Syarat untuk update berdasarkan ID
                    array('%s', '%s', '%s', '%s'),  // Format tipe data
                    array('%d')  // Format untuk ID
                );

                // Redirect setelah data berhasil diupdate
                echo '<script type="text/javascript">
                    alert("Data berhasil diupdate.");
                    window.location.href = "http://websitekecamatanbintantimur.test/lembaga-organisasi-dan-partai-politik/";
                </script>';
                exit;
            }

            // Tampilkan form untuk mengedit data
            return '
                <form method="post">
                    <label for="uraian">Nama Kelurahan:</label>
                    <input type="text" name="kelurahan" value="' . esc_attr($result->kelurahan) . '"><br>
                    <label for="uraian">Nama Anggota:</label>
                    <input type="text" name="nama" value="' . esc_attr($result->nama) . '"><br>
                    <label for="uraian">Jabatan:</label>
                    <input type="text" name="jabatan" value="' . esc_attr($result->jabatan) . '"><br>
                    <label for="uraian">Keterangan:</label>
                    <input type="text" name="ket" value="' . esc_attr($result->ket) . '"><br>
                    <input type="submit" name="submit" value="Update Data" class="update-button">
                    <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
                </form>';
        } else {
            // Jika data tidak ditemukan
            return '<p>Data tidak ditemukan.</p>';
        }
    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_data_lpm_kelurahan_kijang_kota', 'edit_data_lpm_kelurahan_kijang_kota');

function tambah_data_edit_data_lpm_kelurahan_kijang_kota() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_lpm_kelurahan_kijang_kota';

    if (isset($_POST['submit'])) {
        $kelurahan = sanitize_text_field($_POST['kelurahan']);
        $nama = sanitize_text_field($_POST['nama']);
        $jabatan = sanitize_text_field($_POST['jabatan']);
        $ket = sanitize_text_field($_POST['ket']);

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_lpm_kelurahan_kijang_kota',
            array(
                'nomor' => $new_nomor,
                'kelurahan' => $kelurahan,
                'nama' => $nama,
                'jabatan' => $jabatan,
                'ket' => $ket,
            ),
            array('%d','%s', '%s', '%s', '%s')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/lembaga-organisasi-dan-partai-politik/";
        </script>';
        exit;
    }

    return '
        <form method="post">
        <label for="uraian">Nama Kelurahan:</label>
        <input type="text" name="kelurahan" value="' . esc_attr($result->kelurahan) . '"><br>
        <label for="uraian">Nama Anggota:</label>
        <input type="text" name="nama" value="' . esc_attr($result->nama) . '"><br>
        <label for="uraian">Jabatan:</label>
        <input type="text" name="jabatan" value="' . esc_attr($result->jabatan) . '"><br>
        <label for="uraian">Keterangan:</label>
        <input type="text" name="ket" value="' . esc_attr($result->ket) . '"><br>
        <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_lpm_kelurahan_kijang_kota', 'tambah_data_lpm_kelurahan_kijang_kota');

function data_lpm_kelurahan_sungai_enam() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_lpm_kelurahan_sungai_enam';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
            text-align: left;
        }
        .custom-table th{
            font-size: 10px;
        }    
        .custom-table td{
            font-size: 10px;
        }  
        .custom-table th, .custom-table td {
            padding: 12px;
        }
        .custom-table thead {
            background-color: #f2f2f2;
            color: #333;
        }
        .custom-table tr {
            border-bottom: 1px solid #ddd;
        }
        /* Media Query untuk layar kecil */
        @media (max-width: 768px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font */
            }
        }
        @media (max-width: 480px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font lebih kecil */
            }
        }
        </style>';

        $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
        $output .= '<table class="custom-table">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Kelurahan</th>';
        $output .= '<th style="padding:12px;">Nama</th>';
        $output .= '<th style="padding:12px;">Jabatan</th>';
        $output .= '<th style="padding:12px;">Ket</th>';
                        // Tambahkan kolom "Aksi" jika admin login
                        if (current_user_can('administrator')) {
                            $output .= '<th style="padding:12px;">Aksi</th>';
                        }
        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            $output .= '<td style="padding:12px;">' . format_text(esc_html($row->kelurahan)) . '</td>';
            $output .= '<td style="padding:12px;">' . format_text(esc_html($row->nama)) . '</td>';
            $output .= '<td style="padding:12px;">' . format_text(esc_html($row->jabatan)) . '</td>';
            $output .= '<td style="padding:12px;">' . format_text(esc_html($row->ket)) . '</td>';
            // Jika admin login, tampilkan tombol Edit
            if (current_user_can('administrator')) {
                $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-data-lembaga-pemberdayaan-masyarakat-kelurahan-sungai-enam?id=' . $row->id . '">Edit</a></td>';

            }
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }

    if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-data-lembaga-pemberdayaan-masyarakat-kelurahan-sungai-enam" class="tambah-button">Tambah Data (LPM) Kelurahan Sungai Enam</a>';
        $output .= '</div>';
    }

    return $output;
}

function edit_data_lpm_kelurahan_sungai_enam() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_lpm_kelurahan_sungai_enam'; // Nama tabel yang benar

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_lpm_kelurahan_sungai_enam',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/lembaga-organisasi-dan-partai-politik/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
            // Jika form disubmit untuk update
            if (isset($_POST['submit'])) {
                // Validasi dan sanitasi input dari form
                $kelurahan = sanitize_text_field($_POST['kelurahan']);
                $nama = sanitize_text_field($_POST['nama']);
                $jabatan = sanitize_text_field($_POST['jabatan']);
                $ket = sanitize_text_field($_POST['ket']);

                // Update data ke database
                $wpdb->update(
                    'data_lpm_kelurahan_sungai_enam',
                    array(
                        'kelurahan' => $kelurahan,
                        'nama' => $nama,
                        'jabatan' => $jabatan,
                        'ket' => $ket,
                    ),
                    array('id' => $id),  // Syarat untuk update berdasarkan ID
                    array('%s', '%s', '%s', '%s'),  // Format tipe data
                    array('%d')  // Format untuk ID
                );

                // Redirect setelah data berhasil diupdate
                echo '<script type="text/javascript">
                    alert("Data berhasil diupdate.");
                    window.location.href = "http://websitekecamatanbintantimur.test/lembaga-organisasi-dan-partai-politik/";
                </script>';
                exit;
            }

            // Tampilkan form untuk mengedit data
            return '
                <form method="post">
                    <label for="uraian">Nama Kelurahan:</label>
                    <input type="text" name="kelurahan" value="' . esc_attr($result->kelurahan) . '"><br>
                    <label for="uraian">Nama Anggota:</label>
                    <input type="text" name="nama" value="' . esc_attr($result->nama) . '"><br>
                    <label for="uraian">Jabatan:</label>
                    <input type="text" name="jabatan" value="' . esc_attr($result->jabatan) . '"><br>
                    <label for="uraian">Keterangan:</label>
                    <input type="text" name="ket" value="' . esc_attr($result->ket) . '"><br>
                    <input type="submit" name="submit" value="Update Data" class="update-button">
                    <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
                </form>';
        } else {
            // Jika data tidak ditemukan
            return '<p>Data tidak ditemukan.</p>';
        }
    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_data_lpm_kelurahan_sungai_enam', 'edit_data_lpm_kelurahan_sungai_enam');


function tambah_data_edit_data_lpm_kelurahan_sungai_enam() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_lpm_kelurahan_sungai_enam';

    if (isset($_POST['submit'])) {
        $kelurahan = sanitize_text_field($_POST['kelurahan']);
        $nama = sanitize_text_field($_POST['nama']);
        $jabatan = sanitize_text_field($_POST['jabatan']);
        $ket = sanitize_text_field($_POST['ket']);

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_lpm_kelurahan_sungai_enam',
            array(
                'nomor' => $new_nomor,
                'kelurahan' => $kelurahan,
                'nama' => $nama,
                'jabatan' => $jabatan,
                'ket' => $ket,
            ),
            array('%d','%s', '%s', '%s', '%s')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/lembaga-organisasi-dan-partai-politik/";
        </script>';
        exit;
    }

    return '
        <form method="post">
        <label for="uraian">Nama Kelurahan:</label>
        <input type="text" name="kelurahan" value="' . esc_attr($result->kelurahan) . '"><br>
        <label for="uraian">Nama Anggota:</label>
        <input type="text" name="nama" value="' . esc_attr($result->nama) . '"><br>
        <label for="uraian">Jabatan:</label>
        <input type="text" name="jabatan" value="' . esc_attr($result->jabatan) . '"><br>
        <label for="uraian">Keterangan:</label>
        <input type="text" name="ket" value="' . esc_attr($result->ket) . '"><br>
        <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_lpm_kelurahan_sungai_enam', 'tambah_data_lpm_kelurahan_sungai_enam');

function data_lpm_kelurahan_sungai_lekop() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_lpm_kelurahan_sungai_lekop';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
            text-align: left;
        }
        .custom-table th{
            font-size: 10px;
        }    
        .custom-table td{
            font-size: 10px;
        }  
        .custom-table th, .custom-table td {
            padding: 12px;
        }
        .custom-table thead {
            background-color: #f2f2f2;
            color: #333;
        }
        .custom-table tr {
            border-bottom: 1px solid #ddd;
        }
        /* Media Query untuk layar kecil */
        @media (max-width: 768px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font */
            }
        }
        @media (max-width: 480px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font lebih kecil */
            }
        }
        </style>';

        $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
        $output .= '<table class="custom-table">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Kelurahan</th>';
        $output .= '<th style="padding:12px;">Nama</th>';
        $output .= '<th style="padding:12px;">Jabatan</th>';
        $output .= '<th style="padding:12px;">Ket</th>';
                        // Tambahkan kolom "Aksi" jika admin login
                        if (current_user_can('administrator')) {
                            $output .= '<th style="padding:12px;">Aksi</th>';
                        }
        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            $output .= '<td style="padding:12px;">' . format_text(esc_html($row->kelurahan)) . '</td>';
            $output .= '<td style="padding:12px;">' . format_text(esc_html($row->nama)) . '</td>';
            $output .= '<td style="padding:12px;">' . format_text(esc_html($row->jabatan)) . '</td>';
            $output .= '<td style="padding:12px;">' . format_text(esc_html($row->ket)) . '</td>';
            // Jika admin login, tampilkan tombol Edit
            if (current_user_can('administrator')) {
                $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-data-lembaga-pemberdayaan-masyarakat-kelurahan-sungai-lekop?id=' . $row->id . '">Edit</a></td>';

            }
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }

    if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-data-lembaga-pemberdayaan-masyarakat-kelurahan-sungai-lekop" class="tambah-button">Tambah Data (LPM) Kelurahan Sungai Lekop</a>';
        $output .= '</div>';
    }

    return $output;
}

function edit_data_lpm_kelurahan_sungai_lekop() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_lpm_kelurahan_sungai_lekop'; // Nama tabel yang benar

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_lpm_kelurahan_sungai_lekop',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/lembaga-organisasi-dan-partai-politik/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
            // Jika form disubmit untuk update
            if (isset($_POST['submit'])) {
                // Validasi dan sanitasi input dari form
                $kelurahan = sanitize_text_field($_POST['kelurahan']);
                $nama = sanitize_text_field($_POST['nama']);
                $jabatan = sanitize_text_field($_POST['jabatan']);
                $ket = sanitize_text_field($_POST['ket']);

                // Update data ke database
                $wpdb->update(
                    'data_lpm_kelurahan_sungai_lekop',
                    array(
                        'kelurahan' => $kelurahan,
                        'nama' => $nama,
                        'jabatan' => $jabatan,
                        'ket' => $ket,
                    ),
                    array('id' => $id),  // Syarat untuk update berdasarkan ID
                    array('%s', '%s', '%s', '%s'),  // Format tipe data
                    array('%d')  // Format untuk ID
                );

                // Redirect setelah data berhasil diupdate
                echo '<script type="text/javascript">
                    alert("Data berhasil diupdate.");
                    window.location.href = "http://websitekecamatanbintantimur.test/lembaga-organisasi-dan-partai-politik/";
                </script>';
                exit;
            }

            // Tampilkan form untuk mengedit data
            return '
                <form method="post">
                    <label for="uraian">Nama Kelurahan:</label>
                    <input type="text" name="kelurahan" value="' . esc_attr($result->kelurahan) . '"><br>
                    <label for="uraian">Nama Anggota:</label>
                    <input type="text" name="nama" value="' . esc_attr($result->nama) . '"><br>
                    <label for="uraian">Jabatan:</label>
                    <input type="text" name="jabatan" value="' . esc_attr($result->jabatan) . '"><br>
                    <label for="uraian">Keterangan:</label>
                    <input type="text" name="ket" value="' . esc_attr($result->ket) . '"><br>
                    <input type="submit" name="submit" value="Update Data" class="update-button">
                    <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
                </form>';
        } else {
            // Jika data tidak ditemukan
            return '<p>Data tidak ditemukan.</p>';
        }
    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_data_lpm_kelurahan_sungai_lekop', 'edit_data_lpm_kelurahan_sungai_lekop');


function tambah_data_lpm_kelurahan_sungai_lekop() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_lpm_kelurahan_sungai_lekop';

    if (isset($_POST['submit'])) {
        $kelurahan = sanitize_text_field($_POST['kelurahan']);
        $nama = sanitize_text_field($_POST['nama']);
        $jabatan = sanitize_text_field($_POST['jabatan']);
        $ket = sanitize_text_field($_POST['ket']);

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_lpm_kelurahan_sungai_lekop',
            array(
                'nomor' => $new_nomor,
                'kelurahan' => $kelurahan,
                'nama' => $nama,
                'jabatan' => $jabatan,
                'ket' => $ket,
            ),
            array('%d','%s', '%s', '%s', '%s')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/lembaga-organisasi-dan-partai-politik/";
        </script>';
        exit;
    }

    return '
        <form method="post">
        <label for="uraian">Nama Kelurahan:</label>
        <input type="text" name="kelurahan" value="' . esc_attr($result->kelurahan) . '"><br>
        <label for="uraian">Nama Anggota:</label>
        <input type="text" name="nama" value="' . esc_attr($result->nama) . '"><br>
        <label for="uraian">Jabatan:</label>
        <input type="text" name="jabatan" value="' . esc_attr($result->jabatan) . '"><br>
        <label for="uraian">Keterangan:</label>
        <input type="text" name="ket" value="' . esc_attr($result->ket) . '"><br>
        <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_lpm_kelurahan_sungai_lekop', 'tambah_data_lpm_kelurahan_sungai_lekop');

function data_lpm_kelurahan_gunung_lengkuas() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_lpm_kelurahan_gunung_lengkuas';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
            text-align: left;
        }
        .custom-table th{
            font-size: 10px;
        }    
        .custom-table td{
            font-size: 10px;
        }  
        .custom-table th, .custom-table td {
            padding: 12px;
        }
        .custom-table thead {
            background-color: #f2f2f2;
            color: #333;
        }
        .custom-table tr {
            border-bottom: 1px solid #ddd;
        }
        /* Media Query untuk layar kecil */
        @media (max-width: 768px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font */
            }
        }
        @media (max-width: 480px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font lebih kecil */
            }
        }
        </style>';

        $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
        $output .= '<table class="custom-table">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Kelurahan</th>';
        $output .= '<th style="padding:12px;">Nama</th>';
        $output .= '<th style="padding:12px;">Jabatan</th>';
        $output .= '<th style="padding:12px;">Ket</th>';
                        // Tambahkan kolom "Aksi" jika admin login
                        if (current_user_can('administrator')) {
                            $output .= '<th style="padding:12px;">Aksi</th>';
                        }
        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            $output .= '<td style="padding:12px;">' . format_text(esc_html($row->kelurahan)) . '</td>';
            $output .= '<td style="padding:12px;">' . format_text(esc_html($row->nama)) . '</td>';
            $output .= '<td style="padding:12px;">' . format_text(esc_html($row->jabatan)) . '</td>';
            $output .= '<td style="padding:12px;">' . format_text(esc_html($row->ket)) . '</td>';
            // Jika admin login, tampilkan tombol Edit
            if (current_user_can('administrator')) {
                $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-data-lembaga-pemberdayaan-masyarakat-kelurahan-gunung-lengkuas?id=' . $row->id . '">Edit</a></td>';

            }
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }

    if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-data-lembaga-pemberdayaan-masyarakat-kelurahan-gunung-lengkuas" class="tambah-button">Tambah Data (LPM) Kelurahan Gunung Lengkuas</a>';
        $output .= '</div>';
    }

    return $output;
}

function edit_data_lpm_kelurahan_gunung_lengkuas() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_lpm_kelurahan_gunung_lengkuas'; // Nama tabel yang benar

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_lpm_kelurahan_gunung_lengkuas',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/lembaga-organisasi-dan-partai-politik/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
            // Jika form disubmit untuk update
            if (isset($_POST['submit'])) {
                // Validasi dan sanitasi input dari form
                $kelurahan = sanitize_text_field($_POST['kelurahan']);
                $nama = sanitize_text_field($_POST['nama']);
                $jabatan = sanitize_text_field($_POST['jabatan']);
                $ket = sanitize_text_field($_POST['ket']);

                // Update data ke database
                $wpdb->update(
                    'data_lpm_kelurahan_gunung_lengkuas',
                    array(
                        'kelurahan' => $kelurahan,
                        'nama' => $nama,
                        'jabatan' => $jabatan,
                        'ket' => $ket,
                    ),
                    array('id' => $id),  // Syarat untuk update berdasarkan ID
                    array('%s', '%s', '%s', '%s'),  // Format tipe data
                    array('%d')  // Format untuk ID
                );

                // Redirect setelah data berhasil diupdate
                echo '<script type="text/javascript">
                    alert("Data berhasil diupdate.");
                    window.location.href = "http://websitekecamatanbintantimur.test/lembaga-organisasi-dan-partai-politik/";
                </script>';
                exit;
            }

            // Tampilkan form untuk mengedit data
            return '
                <form method="post">
                    <label for="uraian">Nama Kelurahan:</label>
                    <input type="text" name="kelurahan" value="' . esc_attr($result->kelurahan) . '"><br>
                    <label for="uraian">Nama Anggota:</label>
                    <input type="text" name="nama" value="' . esc_attr($result->nama) . '"><br>
                    <label for="uraian">Jabatan:</label>
                    <input type="text" name="jabatan" value="' . esc_attr($result->jabatan) . '"><br>
                    <label for="uraian">Keterangan:</label>
                    <input type="text" name="ket" value="' . esc_attr($result->ket) . '"><br>
                    <input type="submit" name="submit" value="Update Data" class="update-button">
                    <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
                </form>';
        } else {
            // Jika data tidak ditemukan
            return '<p>Data tidak ditemukan.</p>';
        }
    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_data_lpm_kelurahan_gunung_lengkuas', 'edit_data_lpm_kelurahan_gunung_lengkuas');


function tambah_data_lpm_kelurahan_gunung_lengkuas() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_lpm_kelurahan_gunung_lengkuas';

    if (isset($_POST['submit'])) {
        $kelurahan = sanitize_text_field($_POST['kelurahan']);
        $nama = sanitize_text_field($_POST['nama']);
        $jabatan = sanitize_text_field($_POST['jabatan']);
        $ket = sanitize_text_field($_POST['ket']);

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_lpm_kelurahan_gunung_lengkuas',
            array(
                'nomor' => $new_nomor,
                'kelurahan' => $kelurahan,
                'nama' => $nama,
                'jabatan' => $jabatan,
                'ket' => $ket,
            ),
            array('%d','%s', '%s', '%s', '%s')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/lembaga-organisasi-dan-partai-politik/";
        </script>';
        exit;
    }

    return '
        <form method="post">
        <label for="uraian">Nama Kelurahan:</label>
        <input type="text" name="kelurahan" value="' . esc_attr($result->kelurahan) . '"><br>
        <label for="uraian">Nama Anggota:</label>
        <input type="text" name="nama" value="' . esc_attr($result->nama) . '"><br>
        <label for="uraian">Jabatan:</label>
        <input type="text" name="jabatan" value="' . esc_attr($result->jabatan) . '"><br>
        <label for="uraian">Keterangan:</label>
        <input type="text" name="ket" value="' . esc_attr($result->ket) . '"><br>
        <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_lpm_kelurahan_gunung_lengkuas', 'tambah_data_lpm_kelurahan_gunung_lengkuas');


function  data_daerah_pemilihan_kabupaten_bintan() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_daerah_pemilihan_kabupaten_bintan';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
            text-align: left;
        }
        .custom-table th{
            font-size: 10px;
        }    
        .custom-table td{
            font-size: 10px;
        }  
        .custom-table th, .custom-table td {
            padding: 12px;
        }
        .custom-table thead {
            background-color: #f2f2f2;
            color: #333;
        }
        .custom-table tr {
            border-bottom: 1px solid #ddd;
        }
        /* Media Query untuk layar kecil */
        @media (max-width: 768px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font */
            }
        }
        @media (max-width: 480px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font lebih kecil */
            }
        }
        </style>';

        $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
        $output .= '<table class="custom-table">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Kota</th>';
        $output .= '<th style="padding:12px;">Daerah Pemilihan</th>';
        $output .= '<th style="padding:12px;">Wilayah Dapil</th>';
        $output .= '<th style="padding:12px;">Ket</th>';
                            // Tambahkan kolom "Aksi" jika admin login
                            if (current_user_can('administrator')) {
                                $output .= '<th style="padding:12px;">Aksi</th>';
                            }
        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->kota) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->daerah_pemilihan) . '</td>';
            $output .= '<td style="padding:12px;">' . format_text($row->wilayah_dapil) . '</td>';
            $output .= '<td style="padding:12px;">' . format_text($row->ket) . '</td>';
                        // Jika admin login, tampilkan tombol Edit
                        if (current_user_can('administrator')) {
                            $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-data-daerah-pemilihan-kabupaten-bintan?id=' . $row->id . '">Edit</a></td>';
            
                        }
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }
    if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-data-daerah-pemilihan-kabupaten-bintan" class="tambah-button">Tambah Data Daerah Pemilihan Kabupaten Bintan</a>';
        $output .= '</div>';
    }

    return $output;
}

function edit_data_daerah_pemilihan_kabupaten_bintan() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_daerah_pemilihan_kabupaten_bintan'; // Nama tabel yang benar

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_daerah_pemilihan_kabupaten_bintan',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/partai-politik/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
            // Jika form disubmit untuk update
            if (isset($_POST['submit'])) {
                // Validasi dan sanitasi input dari form
                $kota = sanitize_text_field($_POST['kota']);
                $daerah_pemilihan = sanitize_text_field($_POST['daerah_pemilihan']);
                $wilayah_dapil = sanitize_text_field($_POST['wilayah_dapil']);
                $ket = sanitize_text_field($_POST['ket']);

                // Update data ke database
                $wpdb->update(
                    'data_daerah_pemilihan_kabupaten_bintan',
                    array(
                        'kota' => $kota,
                        'daerah_pemilihan' => $daerah_pemilihan,
                        'wilayah_dapil' => $wilayah_dapil,
                        'ket' => $ket,
                    ),
                    array('id' => $id),  // Syarat untuk update berdasarkan ID
                    array('%s', '%s', '%s', '%s'),  // Format tipe data
                    array('%d')  // Format untuk ID
                );

                // Redirect setelah data berhasil diupdate
                echo '<script type="text/javascript">
                    alert("Data berhasil diupdate.");
                    window.location.href = "http://websitekecamatanbintantimur.test/partai-politik/";
                </script>';
                exit;
            }

            // Tampilkan form untuk mengedit data
            return '
                <form method="post">
                    <label for="uraian">Nama Kota:</label>
                    <input type="text" name="kota" value="' . esc_attr($result->kota) . '"><br>
                    <label for="uraian">Daerah Pemilihan:</label>
                    <input type="text" name="daerah_pemilihan" value="' . esc_attr($result->daerah_pemilihan) . '"><br>
                    <label for="uraian">Wilayah Dapil:</label>
                    <input type="text" name="wilayah_dapil" value="' . esc_attr($result->wilayah_dapil) . '"><br>
                    <label for="uraian">Keterangan:</label>
                    <input type="text" name="ket" value="' . esc_attr($result->ket) . '"><br>
                    <input type="submit" name="submit" value="Update Data" class="update-button">
                    <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
                </form>';
        } else {
            // Jika data tidak ditemukan
            return '<p>Data tidak ditemukan.</p>';
        }
    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_data_daerah_pemilihan_kabupaten_bintan', 'edit_data_daerah_pemilihan_kabupaten_bintan');


function tambah_data_daerah_pemilihan_kabupaten_bintan() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_daerah_pemilihan_kabupaten_bintan';

    if (isset($_POST['submit'])) {
        $kota = sanitize_text_field($_POST['kota']);
        $daerah_pemilihan = sanitize_text_field($_POST['daerah_pemilihan']);
        $wilayah_dapil = sanitize_text_field($_POST['wilayah_dapil']);
        $ket = sanitize_text_field($_POST['ket']);

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_daerah_pemilihan_kabupaten_bintan',
            array(
                'nomor' => $new_nomor,
                'kota' => $kota,
                'daerah_pemilihan' => $daerah_pemilihan,
                'wilayah_dapil' => $wilayah_dapil,
                'ket' => $ket,
            ),
            array('%d','%s', '%s', '%s', '%s')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/partai-politik/";
        </script>';
        exit;
    }

    return '
        <form method="post">
        <label for="uraian">Nama Kota:</label>
        <input type="text" name="kota" value="' . esc_attr($result->kota) . '"><br>
        <label for="uraian">Daerah Pemilihan:</label>
        <input type="text" name="daerah_pemilihan" value="' . esc_attr($result->daerah_pemilihan) . '"><br>
        <label for="uraian">Wilayah Dapil:</label>
        <input type="text" name="wilayah_dapil" value="' . esc_attr($result->wilayah_dapil) . '"><br>
        <label for="uraian">Keterangan:</label>
        <input type="text" name="ket" value="' . esc_attr($result->ket) . '"><br>
        <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_daerah_pemilihan_kabupaten_bintan', 'tambah_data_daerah_pemilihan_kabupaten_bintan');


function  data_partai_politik_kecamatan_bintan_timur() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_partai_politik_kecamatan_bintan_timur';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
            text-align: left;
        }
        .custom-table th{
            font-size: 10px;
        }    
        .custom-table td{
            font-size: 10px;
        }  
        .custom-table th, .custom-table td {
            padding: 12px;
        }
        .custom-table thead {
            background-color: #f2f2f2;
            color: #333;
        }
        .custom-table tr {
            border-bottom: 1px solid #ddd;
        }
        /* Media Query untuk layar kecil */
        @media (max-width: 768px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font */
            }
        }
        @media (max-width: 480px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font lebih kecil */
            }
        }
        </style>';

    $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
    $output .= '<table class="custom-table">';
    $output .= '<thead>';
    $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Nama</th>';
        $output .= '<th style="padding:12px;">Jabatan</th>';
        $output .= '<th style="padding:12px;">Nama Partai Politik</th>';
        $output .= '<th style="padding:12px;">Domisili Partai</th>';
        $output .= '<th style="padding:12px;">No.SK Kepengurusan</th>';
                                    // Tambahkan kolom "Aksi" jika admin login
                            if (current_user_can('administrator')) {
                                $output .= '<th style="padding:12px;">Aksi</th>';
                            }
        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->nama) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->jabatan) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->nama_partai) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->domisili_partai) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->no_sk_pengurusan) . '</td>';
                                    // Jika admin login, tampilkan tombol Edit
                                    if (current_user_can('administrator')) {
                                        $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-data-partai-politik-kecamatan-bintan?id=' . $row->id . '">Edit</a></td>';
                        
                                    }
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }

    if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-data-partai-politik-kecamatan-bintan" class="tambah-button">Tambah Data Partai Politik Yang Ada Dikecamatan Bintan Timur</a>';
        $output .= '</div>';
    }

    return $output;

}

function edit_data_partai_politik_kecamatan_bintan_timur() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_partai_politik_kecamatan_bintan_timur'; // Nama tabel yang benar

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_partai_politik_kecamatan_bintan_timur',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/partai-politik/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
            // Jika form disubmit untuk update
            if (isset($_POST['submit'])) {
                // Validasi dan sanitasi input dari form
                $nama = sanitize_text_field($_POST['nama']);
                $jabatan = sanitize_text_field($_POST['jabatan']);
                $nama_partai = sanitize_text_field($_POST['nama_partai']);
                $domisili_partai = sanitize_text_field($_POST['domisili_partai']);
                $no_sk_pengurusan = sanitize_text_field($_POST['no_sk_pengurusan']);

                // Update data ke database
                $wpdb->update(
                    'data_partai_politik_kecamatan_bintan_timur',
                    array(
                        'nama' => $nama,
                        'jabatan' => $jabatan,
                        'nama_partai' => $nama_partai,
                        'domisili_partai' => $domisili_partai,
                        'no_sk_pengurusan' => $no_sk_pengurusan,
                    ),
                    array('id' => $id),  // Syarat untuk update berdasarkan ID
                    array('%s', '%s', '%s', '%s', '%s'),  // Format tipe data
                    array('%d')  // Format untuk ID
                );

                // Redirect setelah data berhasil diupdate
                echo '<script type="text/javascript">
                    alert("Data berhasil diupdate.");
                    window.location.href = "http://websitekecamatanbintantimur.test/partai-politik/";
                </script>';
                exit;
            }

            // Tampilkan form untuk mengedit data
            return '
                <form method="post">
                    <label for="uraian">Nama:</label>
                    <input type="text" name="nama" value="' . esc_attr($result->nama) . '"><br>
                    <label for="uraian">Jabatan:</label>
                    <input type="text" name="jabatan" value="' . esc_attr($result->jabatan) . '"><br>
                    <label for="uraian">Nama Partai:</label>
                    <input type="text" name="nama_partai" value="' . esc_attr($result->nama_partai) . '"><br>
                    <label for="uraian">Domisili Partai:</label>
                    <input type="text" name="domisili_partai" value="' . esc_attr($result->domisili_partai) . '"><br>
                    <label for="uraian">No.SK Kepengurusan:</label>
                    <input type="text" name="no_sk_pengurusan" value="' . esc_attr($result->no_sk_pengurusan) . '"><br>
                    <input type="submit" name="submit" value="Update Data" class="update-button">
                    <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
                </form>';
        } else {
            // Jika data tidak ditemukan
            return '<p>Data tidak ditemukan.</p>';
        }
    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_data_partai_politik_kecamatan_bintan_timur', 'edit_data_partai_politik_kecamatan_bintan_timur');


function tambah_data_partai_politik_kecamatan_bintan_timur() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_partai_politik_kecamatan_bintan_timur';

    if (isset($_POST['submit'])) {
        $nama = sanitize_text_field($_POST['nama']);
        $jabatan = sanitize_text_field($_POST['jabatan']);
        $nama_partai = sanitize_text_field($_POST['nama_partai']);
        $domisili_partai = sanitize_text_field($_POST['domisili_partai']);
        $no_sk_pengurusan = sanitize_text_field($_POST['no_sk_pengurusan']);

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_partai_politik_kecamatan_bintan_timur',
            array(
                'nomor' => $new_nomor,
                'nama' => $nama,
                'jabatan' => $jabatan,
                'nama_partai' => $nama_partai,
                'domisili_partai' => $domisili_partai,
                'no_sk_pengurusan' => $no_sk_pengurusan,
            ),
            array('%d','%s', '%s', '%s', '%s', '%s')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/partai-politik/";
        </script>';
        exit;
    }

    return '
        <form method="post">
        <label for="uraian">Nama:</label>
        <input type="text" name="nama" value="' . esc_attr($result->nama) . '"><br>
        <label for="uraian">Jabatan:</label>
        <input type="text" name="jabatan" value="' . esc_attr($result->jabatan) . '"><br>
        <label for="uraian">Nama Partai:</label>
        <input type="text" name="nama_partai" value="' . esc_attr($result->nama_partai) . '"><br>
        <label for="uraian">Domisili Partai:</label>
        <input type="text" name="domisili_partai" value="' . esc_attr($result->domisili_partai) . '"><br>
        <label for="uraian">No.SK Kepengurusan:</label>
        <input type="text" name="no_sk_pengurusan" value="' . esc_attr($result->no_sk_pengurusan) . '"><br>
        <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_partai_politik_kecamatan_bintan_timur', 'tambah_data_partai_politik_kecamatan_bintan_timur');


function  data_tps() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_tps';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
            text-align: left;
        }
        .custom-table th{
            font-size: 10px;
        }    
        .custom-table td{
            font-size: 10px;
        }  
        .custom-table th, .custom-table td {
            padding: 12px;
        }
        .custom-table thead {
            background-color: #f2f2f2;
            color: #333;
        }
        .custom-table tr {
            border-bottom: 1px solid #ddd;
        }
        /* Media Query untuk layar kecil */
        @media (max-width: 768px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font */
            }
        }
        @media (max-width: 480px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font lebih kecil */
            }
        }
        </style>';

        $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
        $output .= '<table class="custom-table">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Kelurahan</th>';
        $output .= '<th style="padding:12px;">Jumlah TPS</th>';
        $output .= '<th style="padding:12px;">Ket</th>';
                                    // Tambahkan kolom "Aksi" jika admin login
                                    if (current_user_can('administrator')) {
                                        $output .= '<th style="padding:12px;">Aksi</th>';
                                    }
        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->kelurahan) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->jumlah_tps) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->ket) . '</td>';
                                    // Jika admin login, tampilkan tombol Edit
                                    if (current_user_can('administrator')) {
                                        $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-data-tps?id=' . $row->id . '">Edit</a></td>';
                        
                                    }
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }
    if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-data-tps" class="tambah-button">Tambah Data TPS</a>';
        $output .= '</div>';
    }

    return $output;
}

function edit_data_tps() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_tps'; // Nama tabel yang benar

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_tps',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/partai-politik/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
            // Jika form disubmit untuk update
            if (isset($_POST['submit'])) {
                // Validasi dan sanitasi input dari form
                $kelurahan = sanitize_text_field($_POST['kelurahan']);
                $jumlah_tps = intval($_POST['jumlah_tps']);
                $ket = sanitize_text_field($_POST['ket']);

                // Update data ke database
                $wpdb->update(
                    'data_tps',
                    array(
                        'kelurahan' => $kelurahan,
                        'jumlah_tps' => $jumlah_tps,
                        'ket' => $ket,
                    ),
                    array('id' => $id),  // Syarat untuk update berdasarkan ID
                    array('%s', '%d', '%s'),  // Format tipe data
                    array('%d')  // Format untuk ID
                );

                // Redirect setelah data berhasil diupdate
                echo '<script type="text/javascript">
                    alert("Data berhasil diupdate.");
                    window.location.href = "http://websitekecamatanbintantimur.test/partai-politik/";
                </script>';
                exit;
            }

            // Tampilkan form untuk mengedit data
            return '
                <form method="post">
                    <label for="uraian">Kelurahan:</label>
                    <input type="text" name="kelurahan" value="' . esc_attr($result->kelurahan) . '"><br>
                    <label for="laki">Jumlah TPS:</label>
                    <input type="number" name="jumlah_tps" value="' . esc_attr($result->jumlah_tps) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Keterangan:</label>
                    <input type="text" name="ket" value="' . esc_attr($result->ket) . '"><br>
                    <input type="submit" name="submit" value="Update Data" class="update-button">
                    <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
                </form>';
        } else {
            // Jika data tidak ditemukan
            return '<p>Data tidak ditemukan.</p>';
        }
    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_data_tps', 'edit_data_tps');


function tambah_data_tps() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_tps';

    if (isset($_POST['submit'])) {
        $kelurahan = sanitize_text_field($_POST['kelurahan']);
        $jumlah_tps = intval($_POST['jumlah_tps']);
        $ket = sanitize_text_field($_POST['ket']);

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_tps',
            array(
                'nomor' => $new_nomor,
                'kelurahan' => $kelurahan,
                'jumlah_tps' => $jumlah_tps,
                'ket' => $ket,
            ),
            array('%d','%s', '%s', '%s')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/partai-politik/";
        </script>';
        exit;
    }

    return '
        <form method="post">
        <label for="uraian">Kelurahan:</label>
        <input type="text" name="kelurahan" value="' . esc_attr($result->kelurahan) . '"><br>
        <label for="laki">Jumlah TPS:</label>
        <input type="number" name="jumlah_tps" value="' . esc_attr($result->jumlah_tps) . '" inputmode="numeric" pattern="\d*"><br>
        <label for="uraian">Keterangan:</label>
        <input type="text" name="ket" value="' . esc_attr($result->ket) . '"><br>
        <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_tps', 'tambah_data_tps');


function  data_pemilih_tetap() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_pemilih_tetap';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
            text-align: left;
        }
        .custom-table th{
            font-size: 10px;
        }    
        .custom-table td{
            font-size: 10px;
        }  
        .custom-table th, .custom-table td {
            padding: 12px;
        }
        .custom-table thead {
            background-color: #f2f2f2;
            color: #333;
        }
        .custom-table tr {
            border-bottom: 1px solid #ddd;
        }
        /* Media Query untuk layar kecil */
        @media (max-width: 768px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font */
            }
        }
        @media (max-width: 480px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font lebih kecil */
            }
        }
        </style>';

        $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
        $output .= '<table class="custom-table">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Kelurahan</th>';
        $output .= '<th style="padding:12px;">Jumlah Dpt LK</th>';
        $output .= '<th style="padding:12px;">Jumlah Dpt PR</th>';
        $output .= '<th style="padding:12px;">Total</th>';
                                    // Tambahkan kolom "Aksi" jika admin login
                                    if (current_user_can('administrator')) {
                                        $output .= '<th style="padding:12px;">Aksi</th>';
                                    }
        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->kelurahan) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->jumlah_dpt_lk) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->jumlah_dpt_pr) . '</td>';
            
            // Hitung total
            $total = $row->jumlah_dpt_lk + $row->jumlah_dpt_pr;
            
            // Tampilkan total
            $output .= '<td style="padding:12px;">' . esc_html($total) . '</td>';
                                    // Jika admin login, tampilkan tombol Edit
                                    if (current_user_can('administrator')) {
                                        $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-data-pemilih-tetap?id=' . $row->id . '">Edit</a></td>';
                        
                                    }
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }
    if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-data-pemilih-tetap" class="tambah-button">Tambah Data Pemilih Tetap</a>';
        $output .= '</div>';
    }

    return $output;
}

function edit_data_pemilih_tetap() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_pemilih_tetap'; // Nama tabel yang benar

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_pemilih_tetap',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/partai-politik/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
            // Jika form disubmit untuk update
            if (isset($_POST['submit'])) {
                // Validasi dan sanitasi input dari form
                $kelurahan = sanitize_text_field($_POST['kelurahan']);
                $jumlah_dpt_lk = intval($_POST['jumlah_dpt_lk']);
                $jumlah_dpt_pr = intval($_POST['jumlah_dpt_pr']);

                // Update data ke database
                $wpdb->update(
                    'data_pemilih_tetap',
                    array(
                        'kelurahan' => $kelurahan,
                        'jumlah_dpt_lk' => $jumlah_dpt_lk,
                        'jumlah_dpt_pr' => $jumlah_dpt_pr,
                    ),
                    array('id' => $id),  // Syarat untuk update berdasarkan ID
                    array('%s', '%d', '%d'),  // Format tipe data
                    array('%d')  // Format untuk ID
                );

                // Redirect setelah data berhasil diupdate
                echo '<script type="text/javascript">
                    alert("Data berhasil diupdate.");
                    window.location.href = "http://websitekecamatanbintantimur.test/partai-politik/";
                </script>';
                exit;
            }

            // Tampilkan form untuk mengedit data
            return '
                <form method="post">
                    <label for="uraian">Kelurahan:</label>
                    <input type="text" name="kelurahan" value="' . esc_attr($result->kelurahan) . '"><br>
                    <label for="laki">Jumlah Dpt LK:</label>
                    <input type="number" name="jumlah_dpt_lk" value="' . esc_attr($result->jumlah_dpt_lk) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="perempuan">Jumlah Dpt PR:</label>
                    <input type="number" name="jumlah_dpt_pr" value="' . esc_attr($result->jumlah_dpt_pr) . '" inputmode="numeric" pattern="\d*"><br>
                    <input type="submit" name="submit" value="Update Data" class="update-button">
                    <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
                </form>';
        } else {
            // Jika data tidak ditemukan
            return '<p>Data tidak ditemukan.</p>';
        }
    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_data_pemilih_tetap', 'edit_data_pemilih_tetap');


function tambah_data_pemilih_tetap() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_pemilih_tetap';

    if (isset($_POST['submit'])) {
        $kelurahan = sanitize_text_field($_POST['kelurahan']);
        $jumlah_dpt_lk = intval($_POST['jumlah_dpt_lk']);
        $jumlah_dpt_pr = intval($_POST['jumlah_dpt_pr']);

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_pemilih_tetap',
            array(
                'nomor' => $new_nomor,
                'kelurahan' => $kelurahan,
                'jumlah_dpt_lk' => $jumlah_dpt_lk,
                'jumlah_dpt_pr' => $jumlah_dpt_pr,
            ),
            array('%d','%s', '%d', '%d')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/partai-politik/";
        </script>';
        exit;
    }

    return '
        <form method="post">
        <label for="uraian">Kelurahan:</label>
        <input type="text" name="kelurahan" value="' . esc_attr($result->kelurahan) . '"><br>
        <label for="laki">Jumlah Dpt LK:</label>
        <input type="number" name="jumlah_dpt_lk" value="' . esc_attr($result->jumlah_dpt_lk) . '" inputmode="numeric" pattern="\d*"><br>
        <label for="perempuan">Jumlah Dpt PR:</label>
        <input type="number" name="jumlah_dpt_pr" value="' . esc_attr($result->jumlah_dpt_pr) . '" inputmode="numeric" pattern="\d*"><br>
        <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_pemilih_tetap', 'tambah_data_pemilih_tetap');


function  data_pemilih_disabilitas() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_pemilih_disabilitas';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
            text-align: left;
        }
        .custom-table th{
            font-size: 10px;
        }    
        .custom-table td{
            font-size: 10px;
        }  
        .custom-table th, .custom-table td {
            padding: 12px;
        }
        .custom-table thead {
            background-color: #f2f2f2;
            color: #333;
        }
        .custom-table tr {
            border-bottom: 1px solid #ddd;
        }
        /* Media Query untuk layar kecil */
        @media (max-width: 768px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font */
            }
        }
        @media (max-width: 480px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font lebih kecil */
            }
        }
    </style>';

        $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
        $output .= '<table class="custom-table">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Kelurahan</th>';
        $output .= '<th style="padding:12px;">Jumlah Dpt LK</th>';
        $output .= '<th style="padding:12px;">Jumlah Dpt PR</th>';
        $output .= '<th style="padding:12px;">Total</th>';
                                    // Tambahkan kolom "Aksi" jika admin login
                                    if (current_user_can('administrator')) {
                                        $output .= '<th style="padding:12px;">Aksi</th>';
                                    }
        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->kelurahan) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->jumlah_dpt_lk) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->jumlah_dpt_pr) . '</td>';
            
            // Hitung total
            $total = $row->jumlah_dpt_lk + $row->jumlah_dpt_pr;
            
            // Tampilkan total
            $output .= '<td style="padding:12px;">' . esc_html($total) . '</td>';
                                    // Jika admin login, tampilkan tombol Edit
                                    if (current_user_can('administrator')) {
                                        $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-data-pemilih-disabilitas?id=' . $row->id . '">Edit</a></td>';
                        
                                    }
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }
    if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-data-pemilih-disabilitas" class="tambah-button">Tambah Data Disabilitas</a>';
        $output .= '</div>';
    }

    return $output;
}

function edit_data_pemilih_disabilitas() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_pemilih_disabilitas'; // Nama tabel yang benar

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_pemilih_disabilitas',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/partai-politik/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
            // Jika form disubmit untuk update
            if (isset($_POST['submit'])) {
                // Validasi dan sanitasi input dari form
                $kelurahan = sanitize_text_field($_POST['kelurahan']);
                $jumlah_dpt_lk = intval($_POST['jumlah_dpt_lk']);
                $jumlah_dpt_pr = intval($_POST['jumlah_dpt_pr']);

                // Update data ke database
                $wpdb->update(
                    'data_pemilih_disabilitas',
                    array(
                        'kelurahan' => $kelurahan,
                        'jumlah_dpt_lk' => $jumlah_dpt_lk,
                        'jumlah_dpt_pr' => $jumlah_dpt_pr,
                    ),
                    array('id' => $id),  // Syarat untuk update berdasarkan ID
                    array('%s', '%d', '%d'),  // Format tipe data
                    array('%d')  // Format untuk ID
                );

                // Redirect setelah data berhasil diupdate
                echo '<script type="text/javascript">
                    alert("Data berhasil diupdate.");
                    window.location.href = "http://websitekecamatanbintantimur.test/partai-politik/";
                </script>';
                exit;
            }

            // Tampilkan form untuk mengedit data
            return '
                <form method="post">
                    <label for="uraian">Kelurahan:</label>
                    <input type="text" name="kelurahan" value="' . esc_attr($result->kelurahan) . '"><br>
                    <label for="laki">Jumlah Dpt LK:</label>
                    <input type="number" name="jumlah_dpt_lk" value="' . esc_attr($result->jumlah_dpt_lk) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="perempuan">Jumlah Dpt PR:</label>
                    <input type="number" name="jumlah_dpt_pr" value="' . esc_attr($result->jumlah_dpt_pr) . '" inputmode="numeric" pattern="\d*"><br>
                    <input type="submit" name="submit" value="Update Data" class="update-button">
                    <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
                </form>';
        } else {
            // Jika data tidak ditemukan
            return '<p>Data tidak ditemukan.</p>';
        }
    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_data_pemilih_disabilitas', 'edit_data_pemilih_disabilitas');


function tambah_data_pemilih_disabilitas() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_pemilih_disabilitas';

    if (isset($_POST['submit'])) {
        $kelurahan = sanitize_text_field($_POST['kelurahan']);
        $jumlah_dpt_lk = intval($_POST['jumlah_dpt_lk']);
        $jumlah_dpt_pr = intval($_POST['jumlah_dpt_pr']);

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_pemilih_disabilitas',
            array(
                'nomor' => $new_nomor,
                'kelurahan' => $kelurahan,
                'jumlah_dpt_lk' => $jumlah_dpt_lk,
                'jumlah_dpt_pr' => $jumlah_dpt_pr,
            ),
            array('%d','%s', '%d', '%d')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/partai-politik/";
        </script>';
        exit;
    }

    return '
        <form method="post">
        <label for="uraian">Kelurahan:</label>
        <input type="text" name="kelurahan" value="' . esc_attr($result->kelurahan) . '"><br>
        <label for="laki">Jumlah Dpt LK:</label>
        <input type="number" name="jumlah_dpt_lk" value="' . esc_attr($result->jumlah_dpt_lk) . '" inputmode="numeric" pattern="\d*"><br>
        <label for="perempuan">Jumlah Dpt PR:</label>
        <input type="number" name="jumlah_dpt_pr" value="' . esc_attr($result->jumlah_dpt_pr) . '" inputmode="numeric" pattern="\d*"><br>
        <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_pemilih_disabilitas', 'tambah_data_pemilih_disabilitas');


function  data_penduduk_wni() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_penduduk_wni';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
            text-align: left;
        }
        .custom-table th{
            font-size: 10px;
        }    
        .custom-table td{
            font-size: 10px;
        }  
        .custom-table th, .custom-table td {
            padding: 12px;
        }
        .custom-table thead {
            background-color: #f2f2f2;
            color: #333;
        }
        .custom-table tr {
            border-bottom: 1px solid #ddd;
        }
        /* Media Query untuk layar kecil */
        @media (max-width: 768px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font */
            }
        }
        @media (max-width: 480px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font lebih kecil */
            }
        }
        </style>';

        $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
        $output .= '<table class="custom-table">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Kelurahan</th>';
        $output .= '<th style="padding:12px;">Memiliki KK</th>';
        $output .= '<th style="padding:12px;">Belum Memiliki KK</th>';
        $output .= '<th style="padding:12px;">Jumlah KK</th>';
        $output .= '<th style="padding:12px;">Kepemilikan KK (%)</th>';
                                    // Tambahkan kolom "Aksi" jika admin login
                                    if (current_user_can('administrator')) {
                                        $output .= '<th style="padding:12px;">Aksi</th>';
                                    }
        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->kelurahan) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->memiliki_kk) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->belum_memiliki_kk) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->jumlah_kk) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->kepemilikan_kk) . '%</td>';
                                    // Jika admin login, tampilkan tombol Edit
                                    if (current_user_can('administrator')) {
                                        $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-data-penduduk-wni?id=' . $row->id . '">Edit</a></td>';
                        
                                    }
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }
    if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-data-penduduk-wni" class="tambah-button">Tambah Data Penduduk WNI</a>';
        $output .= '</div>';
    }

    return $output;
}

function edit_data_penduduk_wni() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_penduduk_wni'; // Nama tabel yang benar

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_penduduk_wni',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/data-kependudukan/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
            // Jika form disubmit untuk update
            if (isset($_POST['submit'])) {
                // Validasi dan sanitasi input dari form
                $kelurahan = sanitize_text_field($_POST['kelurahan']);
                $memiliki_kk = intval($_POST['memiliki_kk']);
                $belum_memiliki_kk = intval($_POST['belum_memiliki_kk']);
                $jumlah_kk = intval($_POST['jumlah_kk']);
                $kepemilikan_kk = floatval($_POST['kepemilikan_kk']);

                // Update data ke database
                $wpdb->update(
                    'data_penduduk_wni',
                    array(
                        'kelurahan' => $kelurahan,
                        'memiliki_kk' => $memiliki_kk,
                        'belum_memiliki_kk' => $belum_memiliki_kk,
                        'jumlah_kk' => $jumlah_kk,
                        'kepemilikan_kk' => $kepemilikan_kk,
                        
                    ),
                    array('id' => $id),  // Syarat untuk update berdasarkan ID
                    array('%s', '%d', '%d', '%d', '%.2f'),  // Format tipe data
                    array('%d')  // Format untuk ID
                );

                // Redirect setelah data berhasil diupdate
                echo '<script type="text/javascript">
                    alert("Data berhasil diupdate.");
                    window.location.href = "http://websitekecamatanbintantimur.test/data-kependudukan/";
                </script>';
                exit;
            }

            // Tampilkan form untuk mengedit data
            return '
                <form method="post">
                    <label for="uraian">Kelurahan:</label>
                    <input type="text" name="kelurahan" value="' . esc_attr($result->kelurahan) . '"><br>
                    <label for="uraian">Jumlah Memiliki KK:</label>
                    <input type="number" name="memiliki_kk" value="' . esc_attr($result->memiliki_kk) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Belum Memiliki KK:</label>
                    <input type="number" name="belum_memiliki_kk" value="' . esc_attr($result->belum_memiliki_kk) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah KK:</label>
                    <input type="number" name="jumlah_kk" value="' . esc_attr($result->jumlah_kk) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Total kepemilikan KK:</label>
                    <input type="number" name="kepemilikan_kk" value="' . esc_attr($result->kepemilikan_kk) . '" step="0.01" inputmode="decimal" pattern="[0-9]+(\.[0-9]{1,2})?" /><br>
                    <input type="submit" name="submit" value="Update Data" class="update-button">
                    <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
                </form>';
        } else {
            // Jika data tidak ditemukan
            return '<p>Data tidak ditemukan.</p>';
        }
    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_data_penduduk_wni', 'edit_data_penduduk_wni');

function tambah_data_penduduk_wni() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_penduduk_wni';

    if (isset($_POST['submit'])) {
        $kelurahan = sanitize_text_field($_POST['kelurahan']);
        $memiliki_kk = intval($_POST['memiliki_kk']);
        $belum_memiliki_kk = intval($_POST['belum_memiliki_kk']);
        $jumlah_kk = intval($_POST['jumlah_kk']);
        $kepemilikan_kk = floatval($_POST['kepemilikan_kk']);

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_penduduk_wni',
            array(
                'nomor' => $new_nomor,
                'kelurahan' => $kelurahan,
                'memiliki_kk' => $memiliki_kk,
                'belum_memiliki_kk' => $belum_memiliki_kk,
                'jumlah_kk' => $jumlah_kk,
                'kepemilikan_kk' => $kepemilikan_kk,
            ),
            array('%d', '%s', '%d', '%d', '%d', '%.2f')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/data-kependudukan/";
        </script>';
        exit;
    }

    return '
        <form method="post">
        <label for="uraian">Kelurahan:</label>
        <input type="text" name="kelurahan" value="' . esc_attr($result->kelurahan) . '"><br>
        <label for="uraian">Jumlah Memiliki KK:</label>
        <input type="number" name="memiliki_kk" value="' . esc_attr($result->memiliki_kk) . '" inputmode="numeric" pattern="\d*"><br>
        <label for="uraian">Jumlah Belum Memiliki KK:</label>
        <input type="number" name="belum_memiliki_kk" value="' . esc_attr($result->belum_memiliki_kk) . '" inputmode="numeric" pattern="\d*"><br>
        <label for="uraian">Jumlah KK:</label>
        <input type="number" name="jumlah_kk" value="' . esc_attr($result->jumlah_kk) . '" inputmode="numeric" pattern="\d*"><br>
        <label for="uraian">Jumlah Total kepemilikan KK:</label>
        <input type="number" name="kepemilikan_kk" value="' . esc_attr($result->kepemilikan_kk) . '" step="0.01" inputmode="decimal" pattern="[0-9]+(\.[0-9]{1,2})?" /><br>
        <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_penduduk_wni', 'tambah_data_penduduk_wni');


function  data_kepemilikan_kk() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_kepemilikan_kk';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
            text-align: left;
        }
        .custom-table th{
            font-size: 10px;
        }    
        .custom-table td{
            font-size: 10px;
        }  
        .custom-table th, .custom-table td {
            padding: 12px;
        }
        .custom-table thead {
            background-color: #f2f2f2;
            color: #333;
        }
        .custom-table tr {
            border-bottom: 1px solid #ddd;
        }
        /* Media Query untuk layar kecil */
        @media (max-width: 768px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font */
            }
        }
        @media (max-width: 480px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font lebih kecil */
            }
        }
    </style>';

        $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
        $output .= '<table class="custom-table">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Kelurahan</th>';
        $output .= '<th style="padding:12px;">KK (LK)</th>';
        $output .= '<th style="padding:12px;">KK (PR)</th>';
        $output .= '<th style="padding:12px;">Jumlah KK</th>';
        $output .= '<th style="padding:12px;">Kepemilikan KK (%)</th>';
         // Tambahkan kolom "Aksi" jika admin login
         if (current_user_can('administrator')) {
            $output .= '<th style="padding:12px;">Aksi</th>';
        }
        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->kelurahan) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->kk_lk) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->kk_pr) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->jumlah_kk) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->kepemilikan_kk) . '%</td>';
             // Jika admin login, tampilkan tombol Edit
             if (current_user_can('administrator')) {
                $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-data-kepala-keluarga?id=' . $row->id . '">Edit</a></td>';

            }
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }
    if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-data-kepala-keluarga" class="tambah-button">Tambah Data Kepala Keluarga</a>';
        $output .= '</div>';
    }

    return $output;
}

function edit_data_kepemilikan_kk() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_kepemilikan_kk'; // Nama tabel yang benar

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_kepemilikan_kk',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/data-kependudukan/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
            // Jika form disubmit untuk update
            if (isset($_POST['submit'])) {
                // Validasi dan sanitasi input dari form
                $kelurahan = sanitize_text_field($_POST['kelurahan']);
                $kk_lk = intval($_POST['kk_lk']);
                $kk_pr = intval($_POST['kk_pr']);
                $jumlah_kk = intval($_POST['jumlah_kk']);
                $kepemilikan_kk = floatval($_POST['kepemilikan_kk']);

                // Update data ke database
                $wpdb->update(
                    'data_kepemilikan_kk',
                    array(
                        'kelurahan' => $kelurahan,
                        'kk_lk' => $kk_lk,
                        'kk_pr' => $kk_pr,
                        'jumlah_kk' => $jumlah_kk,
                        'kepemilikan_kk' => $kepemilikan_kk,
                        
                    ),
                    array('id' => $id),  // Syarat untuk update berdasarkan ID
                    array('%s', '%d', '%d', '%d', '%.2f'),  // Format tipe data
                    array('%d')  // Format untuk ID
                );

                // Redirect setelah data berhasil diupdate
                echo '<script type="text/javascript">
                    alert("Data berhasil diupdate.");
                    window.location.href = "http://websitekecamatanbintantimur.test/data-kependudukan/";
                </script>';
                exit;
            }

            // Tampilkan form untuk mengedit data
            return '
                <form method="post">
                    <label for="uraian">Kelurahan:</label>
                    <input type="text" name="kelurahan" value="' . esc_attr($result->kelurahan) . '"><br>
                    <label for="uraian">Jumlah KK Laki-Laki:</label>
                    <input type="number" name="kk_lk" value="' . esc_attr($result->kk_lk) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah KK Perempuan:</label>
                    <input type="number" name="kk_pr" value="' . esc_attr($result->kk_pr) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah KK:</label>
                    <input type="number" name="jumlah_kk" value="' . esc_attr($result->jumlah_kk) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Total kepemilikan KK:</label>
                    <input type="number" name="kepemilikan_kk" value="' . esc_attr($result->kepemilikan_kk) . '" step="0.01" inputmode="decimal" pattern="[0-9]+(\.[0-9]{1,2})?" /><br>
                    <input type="submit" name="submit" value="Update Data" class="update-button">
                    <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
                </form>';
        } else {
            // Jika data tidak ditemukan
            return '<p>Data tidak ditemukan.</p>';
        }
    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_data_kepemilikan_kk', 'edit_data_kepemilikan_kk');


function tambah_data_kepemilikan_kk() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_kepemilikan_kk';

    if (isset($_POST['submit'])) {
        $kelurahan = sanitize_text_field($_POST['kelurahan']);
        $kk_lk = intval($_POST['kk_lk']);
        $kk_pr = intval($_POST['kk_pr']);
        $jumlah_kk = intval($_POST['jumlah_kk']);
        $kepemilikan_kk = floatval($_POST['kepemilikan_kk']);

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_kepemilikan_kk',
            array(
                'nomor' => $new_nomor,
                'kelurahan' => $kelurahan,
                'kk_lk' => $kk_lk,
                'kk_pr' => $kk_pr,
                'jumlah_kk' => $jumlah_kk,
                'kepemilikan_kk' => $kepemilikan_kk,
            ),
            array('%d', '%s', '%d', '%d', '%d', '%.2f')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/data-kependudukan/";
        </script>';
        exit;
    }

    return '
        <form method="post">
        <label for="uraian">Kelurahan:</label>
        <input type="text" name="kelurahan" value="' . esc_attr($result->kelurahan) . '"><br>
        <label for="uraian">Jumlah KK Laki-Laki:</label>
        <input type="number" name="kk_lk" value="' . esc_attr($result->kk_lk) . '" inputmode="numeric" pattern="\d*"><br>
        <label for="uraian">Jumlah KK Perempuan:</label>
        <input type="number" name="kk_pr" value="' . esc_attr($result->kk_pr) . '" inputmode="numeric" pattern="\d*"><br>
        <label for="uraian">Jumlah KK:</label>
        <input type="number" name="jumlah_kk" value="' . esc_attr($result->jumlah_kk) . '" inputmode="numeric" pattern="\d*"><br>
        <label for="uraian">Jumlah Total kepemilikan KK:</label>
        <input type="number" name="kepemilikan_kk" value="' . esc_attr($result->kepemilikan_kk) . '" step="0.01" inputmode="decimal" pattern="[0-9]+(\.[0-9]{1,2})?" /><br>
        <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_kepemilikan_kk', 'tambah_data_kepemilikan_kk');


function  data_penduduk_berdasarkan_agama() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_penduduk_berdasarkan_agama';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
            text-align: left;
        }
        .custom-table th{
            font-size: 10px;
        }    
        .custom-table td{
            font-size: 10px;
        }  
        .custom-table th, .custom-table td {
            padding: 12px;
        }
        .custom-table thead {
            background-color: #f2f2f2;
            color: #333;
        }
        .custom-table tr {
            border-bottom: 1px solid #ddd;
        }
        /* Media Query untuk layar kecil */
        @media (max-width: 768px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font */
            }
        }
        @media (max-width: 480px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font lebih kecil */
            }
        }
    </style>';

        $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
        $output .= '<table class="custom-table">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Kelurahan</th>';
        $output .= '<th style="padding:12px;">Islam</th>';
        $output .= '<th style="padding:12px;">Khatolik</th>';
        $output .= '<th style="padding:12px;">Kristen</th>';
        $output .= '<th style="padding:12px;">Hindu</th>';
        $output .= '<th style="padding:12px;">Budha</th>';
        $output .= '<th style="padding:12px;">KhongHuchu</th>';
        $output .= '<th style="padding:12px;">Kepercayaan</th>';
        $output .= '<th style="padding:12px;">Total</th>';
                                    // Tambahkan kolom "Aksi" jika admin login
                                    if (current_user_can('administrator')) {
                                        $output .= '<th style="padding:12px;">Aksi</th>';
                                    }
        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->kelurahan) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->islam) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->khatolik) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->kristen) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->hindu) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->budha) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->khonghuchu) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->kepercayaan) . '</td>';
            $total = $row->islam + $row->khatolik+ $row->kristen+ $row->hindu + $row->budha + $row->khonghuchu + $row->kepercayaan;
            $output .= '<td style="padding:12px;">' . esc_html($total) . '</td>';

                                    // Jika admin login, tampilkan tombol Edit
                                    if (current_user_can('administrator')) {
                                        $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-data-penduduk-berdasarkan-agama?id=' . $row->id . '">Edit</a></td>';
                        
                                    }
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }
    if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-data-penduduk-berdasarkan-agama" class="tambah-button">Tambah Data Penduduk Berdasarkan Agama</a>';
        $output .= '</div>';
    }

    return $output;
}

function edit_data_penduduk_berdasarkan_agama() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_penduduk_berdasarkan_agama'; // Nama tabel yang benar

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_penduduk_berdasarkan_agama',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/data-kependudukan/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
            // Jika form disubmit untuk update
            if (isset($_POST['submit'])) {
                // Validasi dan sanitasi input dari form
                $kelurahan = sanitize_text_field($_POST['kelurahan']);
                $islam = intval($_POST['islam']);
                $khatolik = intval($_POST['khatolik']);
                $kristen = intval($_POST['kristen']);
                $hindu = intval($_POST['hindu']);
                $budha = intval($_POST['budha']);
                $khonghuchu = intval($_POST['khonghuchu']);
                $kepercayaan = intval($_POST['kepercayaan']);

                // Update data ke database
                $wpdb->update(
                    'data_penduduk_berdasarkan_agama',
                    array(
                        'kelurahan' => $kelurahan,
                        'islam' => $islam,
                        'khatolik' => $khatolik,
                        'kristen' => $kristen,
                        'hindu' => $hindu,
                        'budha' => $budha,
                        'khonghuchu' => $khonghuchu,
                        'kepercayaan' => $kepercayaan,
                        
                    ),
                    array('id' => $id),  // Syarat untuk update berdasarkan ID
                    array('%s', '%d', '%d', '%d', '%d', '%d', '%d', '%d'),  // Format tipe data
                    array('%d')  // Format untuk ID
                );

                // Redirect setelah data berhasil diupdate
                echo '<script type="text/javascript">
                    alert("Data berhasil diupdate.");
                    window.location.href = "http://websitekecamatanbintantimur.test/data-kependudukan/";
                </script>';
                exit;
            }

            // Tampilkan form untuk mengedit data
            return '
                <form method="post">
                    <label for="uraian">Kelurahan:</label>
                    <input type="text" name="kelurahan" value="' . esc_attr($result->kelurahan) . '"><br>
                    <label for="uraian">Jumlah Warga Penganut Agama Islam:</label>
                    <input type="number" name="islam" value="' . esc_attr($result->islam) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Warga Penganut Agama Khatolik:</label>
                    <input type="number" name="khatolik" value="' . esc_attr($result->khatolik) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Warga Penganut Agama Kristen:</label>
                    <input type="number" name="kristen" value="' . esc_attr($result->kristen) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Warga Penganut Agama Hindu:</label>
                    <input type="number" name="hindu" value="' . esc_attr($result->hindu) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Warga Penganut Agama KhongHuchu:</label>
                    <input type="number" name="khonghuchu" value="' . esc_attr($result->khonghuchu) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Warga Penganut Agama Budha:</label>
                    <input type="number" name="budha" value="' . esc_attr($result->budha) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Warga Penganut Kepercayaan:</label>
                    <input type="number" name="kepercayaan" value="' . esc_attr($result->kepercayaan) . '" inputmode="numeric" pattern="\d*"><br>
                    <input type="submit" name="submit" value="Update Data" class="update-button">
                    <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
                </form>';
        } else {
            // Jika data tidak ditemukan
            return '<p>Data tidak ditemukan.</p>';
        }
    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_data_penduduk_berdasarkan_agama', 'edit_data_penduduk_berdasarkan_agama');


function tambah_data_penduduk_berdasarkan_agama() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_penduduk_berdasarkan_agama';

    if (isset($_POST['submit'])) {
        $kelurahan = sanitize_text_field($_POST['kelurahan']);
        $islam = intval($_POST['islam']);
        $khatolik = intval($_POST['khatolik']);
        $kristen = intval($_POST['kristen']);
        $hindu = intval($_POST['hindu']);
        $budha = intval($_POST['budha']);
        $khonghuchu = intval($_POST['khonghuchu']);
        $kepercayaan = intval($_POST['kepercayaan']);

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_penduduk_berdasarkan_agama',
            array(
                'nomor' => $new_nomor,
                'kelurahan' => $kelurahan,
                'islam' => $islam,
                'khatolik' => $khatolik,
                'kristen' => $kristen,
                'hindu' => $hindu,
                'budha' => $budha,
                'khonghuchu' => $khonghuchu,
                'kepercayaan' => $kepercayaan,
            ),
            array('%d', '%s', '%d', '%d', '%d', '%d', '%d', '%d', '%d')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/data-kependudukan/";
        </script>';
        exit;
    }

    return '
        <form method="post">
        <label for="uraian">Kelurahan:</label>
        <input type="text" name="kelurahan" value="' . esc_attr($result->kelurahan) . '"><br>
        <label for="uraian">Jumlah Warga Penganut Agama Islam:</label>
        <input type="number" name="islam" value="' . esc_attr($result->islam) . '" inputmode="numeric" pattern="\d*"><br>
        <label for="uraian">Jumlah Warga Penganut Agama Khatolik:</label>
        <input type="number" name="khatolik" value="' . esc_attr($result->khatolik) . '" inputmode="numeric" pattern="\d*"><br>
        <label for="uraian">Jumlah Warga Penganut Agama Kristen:</label>
        <input type="number" name="kristen" value="' . esc_attr($result->kristen) . '" inputmode="numeric" pattern="\d*"><br>
        <label for="uraian">Jumlah Warga Penganut Agama Hindu:</label>
        <input type="number" name="hindu" value="' . esc_attr($result->hindu) . '" inputmode="numeric" pattern="\d*"><br>
        <label for="uraian">Jumlah Warga Penganut Agama KhongHuchu:</label>
        <input type="number" name="khonghuchu" value="' . esc_attr($result->khonghuchu) . '" inputmode="numeric" pattern="\d*"><br>
        <label for="uraian">Jumlah Warga Penganut Agama Budha:</label>
        <input type="number" name="budha" value="' . esc_attr($result->budha) . '" inputmode="numeric" pattern="\d*"><br>
        <label for="uraian">Jumlah Warga Penganut Kepercayaan:</label>
        <input type="number" name="kepercayaan" value="' . esc_attr($result->kepercayaan) . '" inputmode="numeric" pattern="\d*"><br>
        <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_penduduk_berdasarkan_agama', 'tambah_data_penduduk_berdasarkan_agama');


function  data_penduduk_berdasarkan_pendidikan() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_penduduk_berdasarkan_pendidikan';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
            text-align: left;
        }
        .custom-table th{
            font-size: 10px;
        }    
        .custom-table td{
            font-size: 10px;
        }  
        .custom-table th, .custom-table td {
            padding: 12px;
        }
        .custom-table thead {
            background-color: #f2f2f2;
            color: #333;
        }
        .custom-table tr {
            border-bottom: 1px solid #ddd;
        }
        /* Media Query untuk layar kecil */
        @media (max-width: 768px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font */
            }
        }
        @media (max-width: 480px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font lebih kecil */
            }
        }
        </style>';

        $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
        $output .= '<table class="custom-table">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Kelurahan</th>';
        $output .= '<th style="padding:12px;">TDK / BLM SKL</th>';
        $output .= '<th style="padding:12px;">BLM TMT SD / SEDERAJAT</th>';
        $output .= '<th style="padding:12px;">TAMAT SD / SEDERAJAT</th>';
        $output .= '<th style="padding:12px;">SLTP / SEDERAJAT</th>';
        $output .= '<th style="padding:12px;">SLTA / SEDERAJAT</th>';
        $output .= '<th style="padding:12px;">DI / II</th>';
        $output .= '<th style="padding:12px;">AKADEMI / DIII / S.MUDA</th>';
        $output .= '<th style="padding:12px;">DIV / STRATA I</th>';
        $output .= '<th style="padding:12px;">STRATA II</th>';
        $output .= '<th style="padding:12px;">STRATA III</th>';
        $output .= '<th style="padding:12px;">Total</th>';
                                    // Tambahkan kolom "Aksi" jika admin login
                                    if (current_user_can('administrator')) {
                                        $output .= '<th style="padding:12px;">Aksi</th>';
                                    }
        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->kelurahan) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->tdk_blm_skl) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->blm_tmt_sd) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->tmt_sd) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->sltp) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->slta) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->di) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->diii) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->strti) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->strtii) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->strtiii) . '</td>';

            $total = $row->tdk_blm_skl + $row->blm_tmt_sd + $row->tmt_sd + $row->sltp + $row->slta + $row->di + $row->diii + $row->strti + $row->strtii + $row->strtiii;
            $output .= '<td style="padding:12px;">' . esc_html($total) . '</td>';
                                    // Jika admin login, tampilkan tombol Edit
                                    if (current_user_can('administrator')) {
                                        $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-data-penduduk-berdasarkan-pendidikan?id=' . $row->id . '">Edit</a></td>';
                        
                                    }
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }
    if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-data-penduduk-berdasarkan-pendidikan" class="tambah-button">Tambah Data Penduduk Berdasarkan Pendidikan</a>';
        $output .= '</div>';
    }
    return $output;
}

function edit_data_penduduk_berdasarkan_pendidikan() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_penduduk_berdasarkan_pendidikan'; // Nama tabel yang benar

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_penduduk_berdasarkan_pendidikan',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/data-kependudukan/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
            // Jika form disubmit untuk update
            if (isset($_POST['submit'])) {
                // Validasi dan sanitasi input dari form
                $kelurahan = sanitize_text_field($_POST['kelurahan']);
                $tdk_blm_skl = intval($_POST['tdk_blm_skl']);
                $blm_tmt_sd = intval($_POST['blm_tmt_sd']);
                $tmt_sd = intval($_POST['tmt_sd']);
                $sltp = intval($_POST['sltp']);
                $slta = intval($_POST['slta']);
                $di = intval($_POST['di']);
                $diii = intval($_POST['diii']);
                $strti = intval($_POST['strti']);
                $strtii = intval($_POST['strtii']);
                $strtiii = intval($_POST['strtiii']);

                // Update data ke database
                $wpdb->update(
                    'data_penduduk_berdasarkan_pendidikan',
                    array(
                        'kelurahan' => $kelurahan,
                        'tdk_blm_skl' => $tdk_blm_skl,
                        'blm_tmt_sd' => $blm_tmt_sd,
                        'tmt_sd' => $tmt_sd,
                        'sltp' => $sltp,
                        'slta' => $slta,
                        'di' => $di,
                        'diii' => $diii,
                        'strti' => $strti,
                        'strtii' => $strtii,
                        'strtiii' => $strtiii,
                        
                    ),
                    array('id' => $id),  // Syarat untuk update berdasarkan ID
                    array('%s', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d'),  // Format tipe data
                    array('%d')  // Format untuk ID
                );

                // Redirect setelah data berhasil diupdate
                echo '<script type="text/javascript">
                    alert("Data berhasil diupdate.");
                    window.location.href = "http://websitekecamatanbintantimur.test/data-kependudukan/";
                </script>';
                exit;
            }

            // Tampilkan form untuk mengedit data
            return '
                <form method="post">
                    <label for="uraian">Kelurahan:</label>
                    <input type="text" name="kelurahan" value="' . esc_attr($result->kelurahan) . '"><br>
                    <label for="uraian">Jumlah Warga Tidak/Belum Sekolah:</label>
                    <input type="number" name="tdk_blm_skl" value="' . esc_attr($result->tdk_blm_skl) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Warga Belum Tamat SD/Sederajat:</label>
                    <input type="number" name="blm_tmt_sd" value="' . esc_attr($result->blm_tmt_sd) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Warga Tamat SD/Sederajat:</label>
                    <input type="number" name="tmt_sd" value="' . esc_attr($result->tmt_sd) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Warga Tamat Sekolah Lanjutan Tingkat Pertama/Sederajat:</label>
                    <input type="number" name="sltp" value="' . esc_attr($result->sltp) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Warga Tamat Sekolah Lanjutan Tingkat Atas /Sederaja:</label>
                    <input type="number" name="slta" value="' . esc_attr($result->slta) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Warga Tamat DI/DII:</label>
                    <input type="number" name="di" value="' . esc_attr($result->di) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Warga Tamat Akademi/DII/S.Muda:</label>
                    <input type="number" name="diii" value="' . esc_attr($result->diii) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Warga Tamat DIV/STRATA I:</label>
                    <input type="number" name="strti" value="' . esc_attr($result->strti) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Warga Tamat STRATA II:</label>
                    <input type="number" name="strtii" value="' . esc_attr($result->strtii) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Warga Tamat STRATA III:</label>
                    <input type="number" name="strtiii" value="' . esc_attr($result->strtiii) . '" inputmode="numeric" pattern="\d*"><br>
                    <input type="submit" name="submit" value="Update Data" class="update-button">
                    <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
                </form>';
        } else {
            // Jika data tidak ditemukan
            return '<p>Data tidak ditemukan.</p>';
        }
    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_data_penduduk_berdasarkan_pendidikan', 'edit_data_penduduk_berdasarkan_pendidikan');


function tambah_data_penduduk_berdasarkan_pendidikan() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_penduduk_berdasarkan_pendidikan';

    if (isset($_POST['submit'])) {
        $kelurahan = sanitize_text_field($_POST['kelurahan']);
        $tdk_blm_skl = intval($_POST['tdk_blm_skl']);
        $blm_tmt_sd = intval($_POST['blm_tmt_sd']);
        $tmt_sd = intval($_POST['tmt_sd']);
        $sltp = intval($_POST['sltp']);
        $slta = intval($_POST['slta']);
        $di = intval($_POST['di']);
        $diii = intval($_POST['diii']);
        $strti = intval($_POST['strti']);
        $strtii = intval($_POST['strtii']);
        $strtiii = intval($_POST['strtiii']);

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_penduduk_berdasarkan_pendidikan',
            array(
                'nomor' => $new_nomor,
                'kelurahan' => $kelurahan,
                'tdk_blm_skl' => $tdk_blm_skl,
                'blm_tmt_sd' => $blm_tmt_sd,
                'tmt_sd' => $tmt_sd,
                'sltp' => $sltp,
                'slta' => $slta,
                'di' => $di,
                'diii' => $diii,
                'strti' => $strti,
                'strtii' => $strtii,
                'strtiii' => $strtiii,
            ),
            array('%d', '%s', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/data-kependudukan/";
        </script>';
        exit;
    }

    return '
        <form method="post">
        <label for="uraian">Kelurahan:</label>
        <input type="text" name="kelurahan" value="' . esc_attr($result->kelurahan) . '"><br>
        <label for="uraian">Jumlah Warga Tidak/Belum Sekolah:</label>
        <input type="number" name="tdk_blm_skl" value="' . esc_attr($result->tdk_blm_skl) . '" inputmode="numeric" pattern="\d*"><br>
        <label for="uraian">Jumlah Warga Belum Tamat SD/Sederajat:</label>
        <input type="number" name="blm_tmt_sd" value="' . esc_attr($result->blm_tmt_sd) . '" inputmode="numeric" pattern="\d*"><br>
        <label for="uraian">Jumlah Warga Tamat SD/Sederajat:</label>
        <input type="number" name="tmt_sd" value="' . esc_attr($result->tmt_sd) . '" inputmode="numeric" pattern="\d*"><br>
        <label for="uraian">Jumlah Warga Tamat Sekolah Lanjutan Tingkat Pertama/Sederajat:</label>
        <input type="number" name="sltp" value="' . esc_attr($result->sltp) . '" inputmode="numeric" pattern="\d*"><br>
        <label for="uraian">Jumlah Warga Tamat Sekolah Lanjutan Tingkat Atas /Sederaja:</label>
        <input type="number" name="slta" value="' . esc_attr($result->slta) . '" inputmode="numeric" pattern="\d*"><br>
        <label for="uraian">Jumlah Warga Tamat DI/DII:</label>
        <input type="number" name="di" value="' . esc_attr($result->di) . '" inputmode="numeric" pattern="\d*"><br>
        <label for="uraian">Jumlah Warga Tamat Akademi/DII/S.Muda:</label>
        <input type="number" name="diii" value="' . esc_attr($result->diii) . '" inputmode="numeric" pattern="\d*"><br>
        <label for="uraian">Jumlah Warga Tamat DIV/STRATA I:</label>
        <input type="number" name="strti" value="' . esc_attr($result->strti) . '" inputmode="numeric" pattern="\d*"><br>
        <label for="uraian">Jumlah Warga Tamat STRATA II:</label>
        <input type="number" name="strtii" value="' . esc_attr($result->strtii) . '" inputmode="numeric" pattern="\d*"><br>
        <label for="uraian">Jumlah Warga Tamat STRATA III:</label>
        <input type="number" name="strtiii" value="' . esc_attr($result->strtiii) . '" inputmode="numeric" pattern="\d*"><br>
        <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_penduduk_berdasarkan_pendidikan', 'tambah_data_penduduk_berdasarkan_pendidikan');


function  data_penduduk_berdasarkan_umur() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_penduduk_berdasarkan_umur';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
            .custom-table {
                width: 100%;
                border-collapse: collapse;
                margin: 20px 0;
                font-size: 9px;
                text-align: left;
            }
            .custom-table th{
                font-size: 10px;
            }    
            .custom-table td{
                font-size: 10px;
            }  
            .custom-table th, .custom-table td {
                padding: 12px;
            }
            .custom-table thead {
                background-color: #f2f2f2;
                color: #333;
            }
            .custom-table tr {
                border-bottom: 1px solid #ddd;
            }
            /* Media Query untuk layar kecil */
            @media (max-width: 768px) {
                .custom-table {
                    font-size: 10px; /* Mengubah ukuran font */
                }
            }
            @media (max-width: 480px) {
                .custom-table {
                    font-size: 10px; /* Mengubah ukuran font lebih kecil */
                }
            }
        </style>';

        $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
        $output .= '<table class="custom-table">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Kelurahan</th>';
        $output .= '<th style="padding:12px;">00 - 09 TH</th>';
        $output .= '<th style="padding:12px;">10 - 19 TH</th>';
        $output .= '<th style="padding:12px;">20 - 29 TH</th>';
        $output .= '<th style="padding:12px;">30 - 39 TH</th>';
        $output .= '<th style="padding:12px;">40 - 49 TH</th>';
        $output .= '<th style="padding:12px;">50 - 59 TH</th>';
        $output .= '<th style="padding:12px;">60 - 69 TH</th>';
        $output .= '<th style="padding:12px;">70 - 74 TH</th>';
        $output .= '<th style="padding:12px;">> 75 TH</th>';
        $output .= '<th style="padding:12px;">Total</th>';
                                    // Tambahkan kolom "Aksi" jika admin login
                                    if (current_user_can('administrator')) {
                                        $output .= '<th style="padding:12px;">Aksi</th>';
                                    }
        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->kelurahan) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->nol_sembilan_tahun) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->sepuluh_sembilanbelas_tahun) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->duapuluh_duasembilan_tahun) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->tigapuluh_tigasembilan_tahun) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->empatpuluh_empatsembilan_tahun) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->limapuluh_limasembilan_tahun) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->enampuluh_enamsembilan_tahun) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->tujuhpuluh_tujuhempat_tahun) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->tujuhlima_keatas) . '</td>';

            $total = $row->nol_sembilan_tahun + $row->sepuluh_sembilanbelas_tahun + $row->duapuluh_duasembilan_tahun + $row->tigapuluh_tigasembilan_tahun + $row->empatpuluh_empatsembilan_tahun + $row->limapuluh_limasembilan_tahun + $row->enampuluh_enamsembilan_tahun + $row->tujuhpuluh_tujuhempat_tahun + $row->tujuhlima_keatas;
            $output .= '<td style="padding:12px;">' . esc_html($total) . '</td>';
                                    // Jika admin login, tampilkan tombol Edit
                                    if (current_user_can('administrator')) {
                                        $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-data-penduduk-berdasarkan-kelompok-umur?id=' . $row->id . '">Edit</a></td>';
                        
                                    }
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }

    if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-data-penduduk-berdasarkan-kelompok-umur" class="tambah-button">Tambah Data Penduduk Berdasarkan Kelompok Umur</a>';
        $output .= '</div>';
    }

    return $output;
}

function edit_data_penduduk_berdasarkan_umur() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_penduduk_berdasarkan_umur'; // Nama tabel yang benar

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_penduduk_berdasarkan_umur',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/data-kependudukan/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
            // Jika form disubmit untuk update
            if (isset($_POST['submit'])) {
                // Validasi dan sanitasi input dari form
                $kelurahan = sanitize_text_field($_POST['kelurahan']);
                $nol_sembilan_tahun = intval($_POST['nol_sembilan_tahun']);
                $sepuluh_sembilanbelas_tahun = intval($_POST['sepuluh_sembilanbelas_tahun']);
                $duapuluh_duasembilan_tahun = intval($_POST['duapuluh_duasembilan_tahun']);
                $tigapuluh_tigasembilan_tahun = intval($_POST['tigapuluh_tigasembilan_tahun']);
                $empatpuluh_empatsembilan_tahun = intval($_POST['empatpuluh_empatsembilan_tahun']);
                $limapuluh_limasembilan_tahun = intval($_POST['limapuluh_limasembilan_tahun']);
                $enampuluh_enamsembilan_tahun = intval($_POST['enampuluh_enamsembilan_tahun']);
                $tujuhpuluh_tujuhempat_tahun = intval($_POST['tujuhpuluh_tujuhempat_tahun']);
                $tujuhlima_keatas = intval($_POST['tujuhlima_keatas']);

                // Update data ke database
                $wpdb->update(
                    'data_penduduk_berdasarkan_umur',
                    array(
                        'kelurahan' => $kelurahan,
                        'nol_sembilan_tahun' => $nol_sembilan_tahun,
                        'sepuluh_sembilanbelas_tahun' => $sepuluh_sembilanbelas_tahun,
                        'duapuluh_duasembilan_tahun' => $duapuluh_duasembilan_tahun,
                        'tigapuluh_tigasembilan_tahun' => $tigapuluh_tigasembilan_tahun,
                        'empatpuluh_empatsembilan_tahun' => $empatpuluh_empatsembilan_tahun,
                        'limapuluh_limasembilan_tahun' => $limapuluh_limasembilan_tahun,
                        'enampuluh_enamsembilan_tahun' => $enampuluh_enamsembilan_tahun,
                        'tujuhpuluh_tujuhempat_tahun' => $tujuhpuluh_tujuhempat_tahun,
                        'tujuhlima_keatas' => $tujuhlima_keatas,
                        
                    ),
                    array('id' => $id),  // Syarat untuk update berdasarkan ID
                    array('%s', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d'),  // Format tipe data
                    array('%d')  // Format untuk ID
                );

                // Redirect setelah data berhasil diupdate
                echo '<script type="text/javascript">
                    alert("Data berhasil diupdate.");
                    window.location.href = "http://websitekecamatanbintantimur.test/data-kependudukan/";
                </script>';
                exit;
            }

            // Tampilkan form untuk mengedit data
            return '
                <form method="post">
                    <label for="uraian">Kelurahan:</label>
                    <input type="text" name="kelurahan" value="' . esc_attr($result->kelurahan) . '"><br>
                    <label for="uraian">Jumlah Warga Umur 00 - 09 TH:</label>
                    <input type="number" name="nol_sembilan_tahun" value="' . esc_attr($result->nol_sembilan_tahun) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Warga Umur 10 - 19 TH:</label>
                    <input type="number" name="sepuluh_sembilanbelas_tahun" value="' . esc_attr($result->sepuluh_sembilanbelas_tahun) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Warga Umur 20 - 29 TH:</label>
                    <input type="number" name="duapuluh_duasembilan_tahun" value="' . esc_attr($result->duapuluh_duasembilan_tahun) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Warga Umur 30 - 39 TH:</label>
                    <input type="number" name="tigapuluh_tigasembilan_tahun" value="' . esc_attr($result->tigapuluh_tigasembilan_tahun) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Warga Umur 40 - 49 TH:</label>
                    <input type="number" name="empatpuluh_empatsembilan_tahun" value="' . esc_attr($result->empatpuluh_empatsembilan_tahun) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Warga Umur 50 - 59 TH:</label>
                    <input type="number" name="limapuluh_limasembilan_tahun" value="' . esc_attr($result->limapuluh_limasembilan_tahun) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Warga Umur 60 - 69 TH:</label>
                    <input type="number" name="enampuluh_enamsembilan_tahun" value="' . esc_attr($result->enampuluh_enamsembilan_tahun) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Warga Umur 70 - 74 TH:</label>
                    <input type="number" name="tujuhpuluh_tujuhempat_tahun" value="' . esc_attr($result->tujuhpuluh_tujuhempat_tahun) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Warga Umur >75 TH:</label>
                    <input type="number" name="tujuhlima_keatas" value="' . esc_attr($result->tujuhlima_keatas) . '" inputmode="numeric" pattern="\d*"><br>
                    <input type="submit" name="submit" value="Update Data" class="update-button">
                    <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
                </form>';
        } else {
            // Jika data tidak ditemukan
            return '<p>Data tidak ditemukan.</p>';
        }
    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_data_penduduk_berdasarkan_umur', 'edit_data_penduduk_berdasarkan_umur');


function tambah_data_penduduk_berdasarkan_umur() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_penduduk_berdasarkan_umur';

    if (isset($_POST['submit'])) {
        $kelurahan = sanitize_text_field($_POST['kelurahan']);
        $nol_sembilan_tahun = intval($_POST['nol_sembilan_tahun']);
        $sepuluh_sembilanbelas_tahun = intval($_POST['sepuluh_sembilanbelas_tahun']);
        $duapuluh_duasembilan_tahun = intval($_POST['duapuluh_duasembilan_tahun']);
        $tigapuluh_tigasembilan_tahun = intval($_POST['tigapuluh_tigasembilan_tahun']);
        $empatpuluh_empatsembilan_tahun = intval($_POST['empatpuluh_empatsembilan_tahun']);
        $limapuluh_limasembilan_tahun = intval($_POST['limapuluh_limasembilan_tahun']);
        $enampuluh_enamsembilan_tahun = intval($_POST['enampuluh_enamsembilan_tahun']);
        $tujuhpuluh_tujuhempat_tahun = intval($_POST['tujuhpuluh_tujuhempat_tahun']);
        $tujuhlima_keatas = intval($_POST['tujuhlima_keatas']);

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_penduduk_berdasarkan_umur',
            array(
                'nomor' => $new_nomor,
                'kelurahan' => $kelurahan,
                'nol_sembilan_tahun' => $nol_sembilan_tahun,
                'sepuluh_sembilanbelas_tahun' => $sepuluh_sembilanbelas_tahun,
                'duapuluh_duasembilan_tahun' => $duapuluh_duasembilan_tahun,
                'tigapuluh_tigasembilan_tahun' => $tigapuluh_tigasembilan_tahun,
                'empatpuluh_empatsembilan_tahun' => $empatpuluh_empatsembilan_tahun,
                'limapuluh_limasembilan_tahun' => $limapuluh_limasembilan_tahun,
                'enampuluh_enamsembilan_tahun' => $enampuluh_enamsembilan_tahun,
                'tujuhpuluh_tujuhempat_tahun' => $tujuhpuluh_tujuhempat_tahun,
                'tujuhlima_keatas' => $tujuhlima_keatas,
            ),
            array('%d', '%s', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/data-kependudukan/";
        </script>';
        exit;
    }

    return '
        <form method="post">
                    <label for="uraian">Kelurahan:</label>
                    <input type="text" name="kelurahan" value="' . esc_attr($result->kelurahan) . '"><br>
                    <label for="uraian">Jumlah Warga Umur 00 - 09 TH:</label>
                    <input type="number" name="nol_sembilan_tahun" value="' . esc_attr($result->nol_sembilan_tahun) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Warga Umur 10 - 19 TH:</label>
                    <input type="number" name="sepuluh_sembilanbelas_tahun" value="' . esc_attr($result->sepuluh_sembilanbelas_tahun) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Warga Umur 20 - 29 TH:</label>
                    <input type="number" name="duapuluh_duasembilan_tahun" value="' . esc_attr($result->duapuluh_duasembilan_tahun) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Warga Umur 30 - 39 TH:</label>
                    <input type="number" name="tigapuluh_tigasembilan_tahun" value="' . esc_attr($result->tigapuluh_tigasembilan_tahun) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Warga Umur 40 - 49 TH:</label>
                    <input type="number" name="empatpuluh_empatsembilan_tahun" value="' . esc_attr($result->empatpuluh_empatsembilan_tahun) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Warga Umur 50 - 59 TH:</label>
                    <input type="number" name="limapuluh_limasembilan_tahun" value="' . esc_attr($result->limapuluh_limasembilan_tahun) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Warga Umur 60 - 69 TH:</label>
                    <input type="number" name="enampuluh_enamsembilan_tahun" value="' . esc_attr($result->enampuluh_enamsembilan_tahun) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Warga Umur 70 - 74 TH:</label>
                    <input type="number" name="tujuhpuluh_tujuhempat_tahun" value="' . esc_attr($result->tujuhpuluh_tujuhempat_tahun) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Warga Umur >75 TH:</label>
                    <input type="number" name="tujuhlima_keatas" value="' . esc_attr($result->tujuhlima_keatas) . '" inputmode="numeric" pattern="\d*"><br>
        <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_penduduk_berdasarkan_umur', 'tambah_data_penduduk_berdasarkan_umur');


function  data_penduduk_berdasarkan_kawin() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_penduduk_berdasarkan_kawin';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
            text-align: left;
        }
        .custom-table th{
            font-size: 10px;
        }    
        .custom-table td{
            font-size: 10px;
        }  
        .custom-table th, .custom-table td {
            padding: 12px;
        }
        .custom-table thead {
            background-color: #f2f2f2;
            color: #333;
        }
        .custom-table tr {
            border-bottom: 1px solid #ddd;
        }
        /* Media Query untuk layar kecil */
        @media (max-width: 768px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font */
            }
        }
        @media (max-width: 480px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font lebih kecil */
            }
        }
    </style>';

        $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
        $output .= '<table class="custom-table">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Kelurahan</th>';
        $output .= '<th style="padding:12px;">Belum Kawin</th>';
        $output .= '<th style="padding:12px;">Kawin</th>';
        $output .= '<th style="padding:12px;">Cerai Hidup</th>';
        $output .= '<th style="padding:12px;">Cerai Mati</th>';
        $output .= '<th style="padding:12px;">Total</th>';
                                    // Tambahkan kolom "Aksi" jika admin login
                                    if (current_user_can('administrator')) {
                                        $output .= '<th style="padding:12px;">Aksi</th>';
                                    }
        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->kelurahan) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->blm_kawin) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->kawin) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->cerai_hidup) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->cerai_mati) . '</td>';

            $total = $row->blm_kawin + $row->kawin + $row->cerai_hidup + $row->cerai_mati;
            $output .= '<td style="padding:12px;">' . esc_html($total) . '</td>';
                                    // Jika admin login, tampilkan tombol Edit
                                    if (current_user_can('administrator')) {
                                        $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-data-penduduk-berdasarkan-status-kawin?id=' . $row->id . '">Edit</a></td>';
                        
                                    }
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }

    if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-data-penduduk-berdasarkan-status-kawin" class="tambah-button">Tambah Data Penduduk Berdasarkan Status Kawin</a>';
        $output .= '</div>';
    }

    return $output;
}

function edit_data_penduduk_berdasarkan_kawin() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_penduduk_berdasarkan_kawin'; // Nama tabel yang benar

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_penduduk_berdasarkan_kawin',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/data-kependudukan/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
            // Jika form disubmit untuk update
            if (isset($_POST['submit'])) {
                // Validasi dan sanitasi input dari form
                $kelurahan = sanitize_text_field($_POST['kelurahan']);
                $blm_kawin = intval($_POST['blm_kawin']);
                $kawin = intval($_POST['kawin']);
                $cerai_hidup = intval($_POST['cerai_hidup']);
                $cerai_mati = intval($_POST['cerai_mati']);

                // Update data ke database
                $wpdb->update(
                    'data_penduduk_berdasarkan_kawin',
                    array(
                        'kelurahan' => $kelurahan,
                        'blm_kawin' => $blm_kawin,
                        'kawin' => $kawin,
                        'cerai_hidup' => $cerai_hidup,
                        'cerai_mati' => $cerai_mati,
                    ),
                    array('id' => $id),  // Syarat untuk update berdasarkan ID
                    array('%s', '%d', '%d', '%d', '%d'),  // Format tipe data
                    array('%d')  // Format untuk ID
                );

                // Redirect setelah data berhasil diupdate
                echo '<script type="text/javascript">
                    alert("Data berhasil diupdate.");
                    window.location.href = "http://websitekecamatanbintantimur.test/data-kependudukan/";
                </script>';
                exit;
            }

            // Tampilkan form untuk mengedit data
            return '
                <form method="post">
                    <label for="uraian">Kelurahan:</label>
                    <input type="text" name="kelurahan" value="' . esc_attr($result->kelurahan) . '"><br>
                    <label for="uraian">Jumlah Warga Belum Kawin:</label>
                    <input type="number" name="blm_kawin" value="' . esc_attr($result->blm_kawin) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Warga Sudah Kawin:</label>
                    <input type="number" name="kawin" value="' . esc_attr($result->kawin) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Warga Cerai Hidup:</label>
                    <input type="number" name="cerai_hidup" value="' . esc_attr($result->cerai_hidup) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Warga Cerai Mati:</label>
                    <input type="number" name="cerai_mati" value="' . esc_attr($result->cerai_mati) . '" inputmode="numeric" pattern="\d*"><br>
                    <input type="submit" name="submit" value="Update Data" class="update-button">
                    <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
                </form>';
        } else {
            // Jika data tidak ditemukan
            return '<p>Data tidak ditemukan.</p>';
        }
    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_data_penduduk_berdasarkan_kawin', 'edit_data_penduduk_berdasarkan_kawin');


function tambah_data_penduduk_berdasarkan_kawin() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_penduduk_berdasarkan_kawin';

    if (isset($_POST['submit'])) {
        $kelurahan = sanitize_text_field($_POST['kelurahan']);
        $blm_kawin = intval($_POST['blm_kawin']);
        $kawin = intval($_POST['kawin']);
        $cerai_hidup = intval($_POST['cerai_hidup']);
        $cerai_mati = intval($_POST['cerai_mati']);

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_penduduk_berdasarkan_kawin',
            array(
                'nomor' => $new_nomor,
                'kelurahan' => $kelurahan,
                'blm_kawin' => $blm_kawin,
                'kawin' => $kawin,
                'cerai_hidup' => $cerai_hidup,
                'cerai_mati' => $cerai_mati,
            ),
            array('%d', '%s', '%d', '%d', '%d', '%d')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/data-kependudukan/";
        </script>';
        exit;
    }

    return '
        <form method="post">
                    <label for="uraian">Kelurahan:</label>
                    <input type="text" name="kelurahan" value="' . esc_attr($result->kelurahan) . '"><br>
                    <label for="uraian">Jumlah Warga Belum Kawin:</label>
                    <input type="number" name="blm_kawin" value="' . esc_attr($result->blm_kawin) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Warga Sudah Kawin:</label>
                    <input type="number" name="kawin" value="' . esc_attr($result->kawin) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Warga Cerai Hidup:</label>
                    <input type="number" name="cerai_hidup" value="' . esc_attr($result->cerai_hidup) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Warga Cerai Mati:</label>
                    <input type="number" name="cerai_mati" value="' . esc_attr($result->cerai_mati) . '" inputmode="numeric" pattern="\d*"><br>
        <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_penduduk_berdasarkan_kawin', 'tambah_data_penduduk_berdasarkan_kawin');


function  data_penduduk_berdasarkan_produktif() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_penduduk_berdasarkan_produktif';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
            text-align: left;
        }
        .custom-table th{
            font-size: 10px;
        }    
        .custom-table td{
            font-size: 10px;
        }  
        .custom-table th, .custom-table td {
            padding: 12px;
        }
        .custom-table thead {
            background-color: #f2f2f2;
            color: #333;
        }
        .custom-table tr {
            border-bottom: 1px solid #ddd;
        }
        /* Media Query untuk layar kecil */
        @media (max-width: 768px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font */
            }
        }
        @media (max-width: 480px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font lebih kecil */
            }
        }
        </style>';

        $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
        $output .= '<table class="custom-table">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Kelurahan</th>';
        $output .= '<th style="padding:12px;">Usia Muda</th>';
        $output .= '<th style="padding:12px;">Produktif</th>';
        $output .= '<th style="padding:12px;">Non Produktif</th>';
        $output .= '<th style="padding:12px;">Total</th>';
                                    // Tambahkan kolom "Aksi" jika admin login
                                    if (current_user_can('administrator')) {
                                        $output .= '<th style="padding:12px;">Aksi</th>';
                                    }
        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->kelurahan) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->usia_muda) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->produktif) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->non_produktif) . '</td>';

            $total = $row->usia_muda + $row->produktif + $row->non_produktif;
            $output .= '<td style="padding:12px;">' . esc_html($total) . '</td>';
                                    // Jika admin login, tampilkan tombol Edit
                                    if (current_user_can('administrator')) {
                                        $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-data-penduduk-berdasarkan-jumlah-usia-produktif?id=' . $row->id . '">Edit</a></td>';
                        
                                    }
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }

    if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-data-penduduk-berdasarkan-jumlah-usia-produktif" class="tambah-button">Tambah Data Penduduk Berdasarkan Jumlah Usia Produktif</a>';
        $output .= '</div>';
    }

    return $output;
}

function edit_data_penduduk_berdasarkan_produktif() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_penduduk_berdasarkan_produktif'; // Nama tabel yang benar

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_penduduk_berdasarkan_produktif',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/data-kependudukan/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
            // Jika form disubmit untuk update
            if (isset($_POST['submit'])) {
                // Validasi dan sanitasi input dari form
                $kelurahan = sanitize_text_field($_POST['kelurahan']);
                $usia_muda = intval($_POST['usia_muda']);
                $produktif = intval($_POST['produktif']);
                $non_produktif = intval($_POST['non_produktif']);

                // Update data ke database
                $wpdb->update(
                    'data_penduduk_berdasarkan_produktif',
                    array(
                        'kelurahan' => $kelurahan,
                        'usia_muda' => $usia_muda,
                        'produktif' => $produktif,
                        'non_produktif' => $non_produktif,
                        
                    ),
                    array('id' => $id),  // Syarat untuk update berdasarkan ID
                    array('%s', '%d', '%d', '%d'),  // Format tipe data
                    array('%d')  // Format untuk ID
                );

                // Redirect setelah data berhasil diupdate
                echo '<script type="text/javascript">
                    alert("Data berhasil diupdate.");
                    window.location.href = "http://websitekecamatanbintantimur.test/data-kependudukan/";
                </script>';
                exit;
            }

            // Tampilkan form untuk mengedit data
            return '
                <form method="post">
                    <label for="uraian">Kelurahan:</label>
                    <input type="text" name="kelurahan" value="' . esc_attr($result->kelurahan) . '"><br>
                    <label for="uraian">Jumlah Warga Usia Muda:</label>
                    <input type="number" name="usia_muda" value="' . esc_attr($result->usia_muda) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Warga Produktif:</label>
                    <input type="number" name="produktif" value="' . esc_attr($result->produktif) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Warga Non Produktif:</label>
                    <input type="number" name="non_produktif" value="' . esc_attr($result->non_produktif) . '" inputmode="numeric" pattern="\d*"><br>
                    <input type="submit" name="submit" value="Update Data" class="update-button">
                    <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
                </form>';
        } else {
            // Jika data tidak ditemukan
            return '<p>Data tidak ditemukan.</p>';
        }
    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_data_penduduk_berdasarkan_produktif', 'edit_data_penduduk_berdasarkan_produktif');


function tambah_data_penduduk_berdasarkan_produktif() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_penduduk_berdasarkan_produktif';

    if (isset($_POST['submit'])) {
        $kelurahan = sanitize_text_field($_POST['kelurahan']);
        $usia_muda = intval($_POST['usia_muda']);
        $produktif = intval($_POST['produktif']);
        $non_produktif = intval($_POST['non_produktif']);
        $cerai_mati = intval($_POST['cerai_mati']);

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_penduduk_berdasarkan_produktif',
            array(
                'nomor' => $new_nomor,
                'kelurahan' => $kelurahan,
                'usia_muda' => $usia_muda,
                'produktif' => $produktif,
                'non_produktif' => $non_produktif,
            ),
            array('%d', '%s', '%d', '%d', '%d')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/data-kependudukan/";
        </script>';
        exit;
    }

    return '
        <form method="post">
                    <label for="uraian">Kelurahan:</label>
                    <input type="text" name="kelurahan" value="' . esc_attr($result->kelurahan) . '"><br>
                    <label for="uraian">Jumlah Warga Usia Muda:</label>
                    <input type="number" name="usia_muda" value="' . esc_attr($result->usia_muda) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Warga Produktif:</label>
                    <input type="number" name="produktif" value="' . esc_attr($result->produktif) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Warga Non Produktif:</label>
                    <input type="number" name="non_produktif" value="' . esc_attr($result->non_produktif) . '" inputmode="numeric" pattern="\d*"><br>
        <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_penduduk_berdasarkan_produktif', 'tambah_data_penduduk_berdasarkan_produktif');


function  data_penduduk_berdasarkan_cacat_mental() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_penduduk_berdasarkan_cacat_mental';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
            text-align: left;
        }
        .custom-table th{
            font-size: 10px;
        }    
        .custom-table td{
            font-size: 10px;
        }  
        .custom-table th, .custom-table td {
            padding: 12px;
        }
        .custom-table thead {
            background-color: #f2f2f2;
            color: #333;
        }
        .custom-table tr {
            border-bottom: 1px solid #ddd;
        }
        /* Media Query untuk layar kecil */
        @media (max-width: 768px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font */
            }
        }
        @media (max-width: 480px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font lebih kecil */
            }
        }
        </style>';

        $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
        $output .= '<table class="custom-table">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Kelurahan</th>';
        $output .= '<th style="padding:12px;">Disabilitas Fisik</th>';
        $output .= '<th style="padding:12px;">Disabilitas Fisik Dan Mental</th>';
        $output .= '<th style="padding:12px;">Disabilitas Netra</th>';
        $output .= '<th style="padding:12px;">Disabilitas Mental / Jiwa</th>';
        $output .= '<th style="padding:12px;">Disabilitas Rungu</th>';
        $output .= '<th style="padding:12px;">Disabilitas Lainnya</th>';
        $output .= '<th style="padding:12px;">Total</th>';
                                   // Tambahkan kolom "Aksi" jika admin login
                                   if (current_user_can('administrator')) {
                                    $output .= '<th style="padding:12px;">Aksi</th>';
                                }
        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->kelurahan) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->disabilitas_fisik) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->disabilitas_fisik_dan_mental) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->disabilitas_netra) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->disabilitas_mental) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->disabilitas_rungu) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->disabilitas_lainnya) . '</td>';

            $total = $row->disabilitas_fisik + $row->disabilitas_fisik_dan_mental + $row->disabilitas_netra + $row->disabilitas_mental + $row->disabilitas_rungu + $row->disabilitas_lainnya;
            $output .= '<td style="padding:12px;">' . esc_html($total) . '</td>';
                                    // Jika admin login, tampilkan tombol Edit
                                    if (current_user_can('administrator')) {
                                        $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-data-penduduk-berdasarkan-cacat-mental-fisik?id=' . $row->id . '">Edit</a></td>';
                        
                                    }
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }

    if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-data-penduduk-berdasarkan-cacat-mental-fisik" class="tambah-button">Tambah Data Penduduk Berdasarkan Cacat Mental/Fisik</a>';
        $output .= '</div>';
    }

    return $output;
}

function edit_data_penduduk_berdasarkan_cacat_mental() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_penduduk_berdasarkan_cacat_mental'; // Nama tabel yang benar

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_penduduk_berdasarkan_cacat_mental',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/data-kependudukan/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
            // Jika form disubmit untuk update
            if (isset($_POST['submit'])) {
                // Validasi dan sanitasi input dari form
                $kelurahan = sanitize_text_field($_POST['kelurahan']);
                $disabilitas_fisik = intval($_POST['disabilitas_fisik']);
                $disabilitas_fisik_dan_mental = intval($_POST['disabilitas_fisik_dan_mental']);
                $disabilitas_netra = intval($_POST['disabilitas_netra']);
                $disabilitas_mental = intval($_POST['disabilitas_mental']);
                $disabilitas_rungu = intval($_POST['disabilitas_rungu']);
                $disabilitas_lainnya = intval($_POST['disabilitas_lainnya']);

                // Update data ke database
                $wpdb->update(
                    'data_penduduk_berdasarkan_cacat_mental',
                    array(
                        'kelurahan' => $kelurahan,
                        'disabilitas_fisik' => $disabilitas_fisik,
                        'disabilitas_fisik_dan_mental' => $disabilitas_fisik_dan_mental,
                        'disabilitas_netra' => $disabilitas_netra,
                        'disabilitas_mental' => $disabilitas_mental,
                        'disabilitas_rungu' => $disabilitas_rungu,
                        'disabilitas_lainnya' => $disabilitas_lainnya,
                        
                    ),
                    array('id' => $id),  // Syarat untuk update berdasarkan ID
                    array('%s', '%d', '%d', '%d', '%d', '%d', '%d'),  // Format tipe data
                    array('%d')  // Format untuk ID
                );

                // Redirect setelah data berhasil diupdate
                echo '<script type="text/javascript">
                    alert("Data berhasil diupdate.");
                    window.location.href = "http://websitekecamatanbintantimur.test/data-kependudukan/";
                </script>';
                exit;
            }

            // Tampilkan form untuk mengedit data
            return '
                <form method="post">
                    <label for="uraian">Kelurahan:</label>
                    <input type="text" name="kelurahan" value="' . esc_attr($result->kelurahan) . '"><br>
                    <label for="uraian">Jumlah Warga Disabilitas Fisik:</label>
                    <input type="number" name="disabilitas_fisik" value="' . esc_attr($result->disabilitas_fisik) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Warga Disabilitas Fisik Dan Mental:</label>
                    <input type="number" name="disabilitas_fisik_dan_mental" value="' . esc_attr($result->disabilitas_fisik_dan_mental) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Warga Disabilitas Netra:</label>
                    <input type="number" name="disabilitas_netra" value="' . esc_attr($result->disabilitas_netra) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Warga Disabilitas Mental / Jiwa:</label>
                    <input type="number" name="disabilitas_mental" value="' . esc_attr($result->disabilitas_mental) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Warga Disabilitas Rungu:</label>
                    <input type="number" name="disabilitas_rungu" value="' . esc_attr($result->disabilitas_rungu) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Warga Disabilitas Lainnya:</label>
                    <input type="number" name="disabilitas_lainnya" value="' . esc_attr($result->disabilitas_lainnya) . '" inputmode="numeric" pattern="\d*"><br>
                    <input type="submit" name="submit" value="Update Data" class="update-button">
                    <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
                </form>';
        } else {
            // Jika data tidak ditemukan
            return '<p>Data tidak ditemukan.</p>';
        }
    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_data_penduduk_berdasarkan_cacat_mental', 'edit_data_penduduk_berdasarkan_cacat_mental');


function tambah_data_penduduk_berdasarkan_cacat_mental() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_penduduk_berdasarkan_cacat_mental';

    if (isset($_POST['submit'])) {
        $kelurahan = sanitize_text_field($_POST['kelurahan']);
        $disabilitas_fisik = intval($_POST['disabilitas_fisik']);
        $disabilitas_fisik_dan_mental = intval($_POST['disabilitas_fisik_dan_mental']);
        $disabilitas_netra = intval($_POST['disabilitas_netra']);
        $disabilitas_mental = intval($_POST['disabilitas_mental']);
        $disabilitas_rungu = intval($_POST['disabilitas_rungu']);
        $disabilitas_lainnya = intval($_POST['disabilitas_lainnya']);

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_penduduk_berdasarkan_cacat_mental',
            array(
                'nomor' => $new_nomor,
                'kelurahan' => $kelurahan,
                'disabilitas_fisik' => $disabilitas_fisik,
                'disabilitas_fisik_dan_mental' => $disabilitas_fisik_dan_mental,
                'disabilitas_netra' => $disabilitas_netra,
                'disabilitas_mental' => $disabilitas_mental,
                'disabilitas_rungu' => $disabilitas_rungu,
                'disabilitas_lainnya' => $disabilitas_lainnya,
            ),
            array('%d', '%s', '%d', '%d', '%d', '%d', '%d', '%d')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/data-kependudukan/";
        </script>';
        exit;
    }

    return '
        <form method="post">
                    <label for="uraian">Kelurahan:</label>
                    <input type="text" name="kelurahan" value="' . esc_attr($result->kelurahan) . '"><br>
                    <label for="uraian">Jumlah Warga Disabilitas Fisik:</label>
                    <input type="number" name="disabilitas_fisik" value="' . esc_attr($result->disabilitas_fisik) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Warga Disabilitas Fisik Dan Mental:</label>
                    <input type="number" name="disabilitas_fisik_dan_mental" value="' . esc_attr($result->disabilitas_fisik_dan_mental) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Warga Disabilitas Netra:</label>
                    <input type="number" name="disabilitas_netra" value="' . esc_attr($result->disabilitas_netra) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Warga Disabilitas Mental / Jiwa:</label>
                    <input type="number" name="disabilitas_mental" value="' . esc_attr($result->disabilitas_mental) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Warga Disabilitas Rungu:</label>
                    <input type="number" name="disabilitas_rungu" value="' . esc_attr($result->disabilitas_rungu) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Warga Disabilitas Lainnya:</label>
                    <input type="number" name="disabilitas_lainnya" value="' . esc_attr($result->disabilitas_lainnya) . '" inputmode="numeric" pattern="\d*"><br>
        <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_penduduk_berdasarkan_cacat_mental', 'tambah_data_penduduk_berdasarkan_cacat_mental');


function  data_jumlah_murid_tpq_tk_paud_sd_mi_smp_mts() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_jumlah_murid_tpq_tk_paud_sd_mi_smp_mts';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
            text-align: left;
        }
        .custom-table th{
            font-size: 10px;
        }    
        .custom-table td{
            font-size: 10px;
        }  
        .custom-table th, .custom-table td {
            padding: 12px;
        }
        .custom-table thead {
            background-color: #f2f2f2;
            color: #333;
        }
        .custom-table tr {
            border-bottom: 1px solid #ddd;
        }
        /* Media Query untuk layar kecil */
        @media (max-width: 768px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font */
            }
        }
        @media (max-width: 480px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font lebih kecil */
            }
        }
        </style>';

        $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
        $output .= '<table class="custom-table">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Sekolah</th>';
        $output .= '<th style="padding:12px;">Murid LK</th>';
        $output .= '<th style="padding:12px;">Murid PR</th>';
        $output .= '<th style="padding:12px;">Jumlah</th>';
        $output .= '<th style="padding:12px;">Ket</th>';
                                    // Tambahkan kolom "Aksi" jika admin login
                                    if (current_user_can('administrator')) {
                                        $output .= '<th style="padding:12px;">Aksi</th>';
                                    }
        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->sekolah) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->murid_lk) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->murid_pr) . '</td>';
            $jumlah = $row->murid_lk + $row->murid_pr;
            $output .= '<td style="padding:12px;">' . esc_html($jumlah) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->ket) . '</td>';
                                    // Jika admin login, tampilkan tombol Edit
                                    if (current_user_can('administrator')) {
                                        $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-data-jumlah-mmurid-tpq-tk-paud-sd-mi-smp-mts?id=' . $row->id . '">Edit</a></td>';
                        
                                    }
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }
    if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-data-jumlah-mmurid-tpq-tk-paud-sd-mi-smp-mts" class="tambah-button">Tambah Data Jumlah Murid TPQ, TK, PAUD, SD, MI, SMP, MTs</a>';
        $output .= '</div>';
    }

    return $output;
}

function edit_data_jumlah_murid_tpq_tk_paud_sd_mi_smp_mts() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_jumlah_murid_tpq_tk_paud_sd_mi_smp_mts'; // Nama tabel yang benar

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_jumlah_murid_tpq_tk_paud_sd_mi_smp_mts',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/data-kepindidikan/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
            // Jika form disubmit untuk update
            if (isset($_POST['submit'])) {
                // Validasi dan sanitasi input dari form
                $sekolah = sanitize_text_field($_POST['sekolah']);
                $murid_lk = intval($_POST['murid_lk']);
                $murid_pr = intval($_POST['murid_pr']);
                $ket = sanitize_text_field($_POST['ket']);


                // Update data ke database
                $wpdb->update(
                    'data_jumlah_murid_tpq_tk_paud_sd_mi_smp_mts',
                    array(
                        'sekolah' => $sekolah,
                        'murid_lk' => $murid_lk,
                        'murid_pr' => $murid_pr,
                        'ket' => $ket,
                    ),
                    array('id' => $id),  // Syarat untuk update berdasarkan ID
                    array('%s', '%d', '%d', '%s'),  // Format tipe data
                    array('%d')  // Format untuk ID
                );

                // Redirect setelah data berhasil diupdate
                echo '<script type="text/javascript">
                    alert("Data berhasil diupdate.");
                    window.location.href = "http://websitekecamatanbintantimur.test/data-kepindidikan/";
                </script>';
                exit;
            }

            // Tampilkan form untuk mengedit data
            return '
                <form method="post">
                    <label for="uraian">Sekolah:</label>
                    <input type="text" name="sekolah" value="' . esc_attr($result->sekolah) . '"><br>
                    <label for="uraian">Murid Laki-Laki:</label>
                    <input type="number" name="murid_lk" value="' . esc_attr($result->murid_lk) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Murid Perempuan:</label>
                    <input type="number" name="murid_pr" value="' . esc_attr($result->murid_pr) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Keterangan:</label>
                    <input type="text" name="ket" value="' . esc_attr($result->ket) . '"><br>
                    <input type="submit" name="submit" value="Update Data" class="update-button">
                    <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
                </form>';
        } else {
            // Jika data tidak ditemukan
            return '<p>Data tidak ditemukan.</p>';
        }
    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_data_jumlah_murid_tpq_tk_paud_sd_mi_smp_mts', 'edit_data_jumlah_murid_tpq_tk_paud_sd_mi_smp_mts');


function tambah_data_jumlah_murid_tpq_tk_paud_sd_mi_smp_mts() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_jumlah_murid_tpq_tk_paud_sd_mi_smp_mts';

    if (isset($_POST['submit'])) {
        $sekolah = sanitize_text_field($_POST['sekolah']);
        $murid_lk = intval($_POST['murid_lk']);
        $murid_pr = intval($_POST['murid_pr']);
        $ket = sanitize_text_field($_POST['ket']);

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_jumlah_murid_tpq_tk_paud_sd_mi_smp_mts',
            array(
                'nomor' => $new_nomor,
                'sekolah' => $sekolah,
                'murid_lk' => $murid_lk,
                'murid_pr' => $murid_pr,
                'ket' => $ket,
            ),
            array('%d', '%s', '%d', '%d', '%s')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/data-kepindidikan/";
        </script>';
        exit;
    }

    return '
        <form method="post">
                    <label for="uraian">Sekolah:</label>
                    <input type="text" name="sekolah" value="' . esc_attr($result->sekolah) . '"><br>
                    <label for="uraian">Murid Laki-Laki:</label>
                    <input type="number" name="murid_lk" value="' . esc_attr($result->murid_lk) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Murid Perempuan:</label>
                    <input type="number" name="murid_pr" value="' . esc_attr($result->murid_pr) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Keterangan:</label>
                    <input type="text" name="ket" value="' . esc_attr($result->ket) . '"><br>
        <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_jumlah_murid_tpq_tk_paud_sd_mi_smp_mts', 'tambah_data_jumlah_murid_tpq_tk_paud_sd_mi_smp_mts');


function  data_jumlah_murid_sma_1_bintan_kelas_x() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_jumlah_murid_sma_1_bintan_kelas_x';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
            text-align: left;
        }
        .custom-table th{
            font-size: 10px;
        }    
        .custom-table td{
            font-size: 10px;
        }  
        .custom-table th, .custom-table td {
            padding: 12px;
        }
        .custom-table thead {
            background-color: #f2f2f2;
            color: #333;
        }
        .custom-table tr {
            border-bottom: 1px solid #ddd;
        }
        /* Media Query untuk layar kecil */
        @media (max-width: 768px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font */
            }
        }
        @media (max-width: 480px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font lebih kecil */
            }
        }
        </style>';

        $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
        $output .= '<table class="custom-table">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Kelas</th>';
        $output .= '<th style="padding:12px;">Jurusan</th>';
        $output .= '<th style="padding:12px;">Murid LK</th>';
        $output .= '<th style="padding:12px;">Murid PR</th>';
        $output .= '<th style="padding:12px;">Jumlah</th>';
        $output .= '<th style="padding:12px;">Ket</th>';
                                    // Tambahkan kolom "Aksi" jika admin login
                                    if (current_user_can('administrator')) {
                                        $output .= '<th style="padding:12px;">Aksi</th>';
                                    }
        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->kelas) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->jurusan) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->murid_lk) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->murid_pr) . '</td>';
            $jumlah = $row->murid_lk + $row->murid_pr;
            $output .= '<td style="padding:12px;">' . esc_html($jumlah) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->ket) . '</td>';
            if (current_user_can('administrator')) {
                $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-data-jumlah-murid-sma-negeri-1-bintan-kelas-x?id=' . $row->id . '">Edit</a></td>';

            }
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }

    if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-data-jumlah-murid-sma-negeri-1-bintan-kelas-x" class="tambah-button">Tambah Data Jumlah Murid SMA Negeri 1 Bintan Kelas X</a>';
        $output .= '</div>';
    }

    return $output;
}

function edit_data_jumlah_murid_sma_1_bintan_kelas_x() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_jumlah_murid_sma_1_bintan_kelas_x'; // Nama tabel yang benar

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_jumlah_murid_sma_1_bintan_kelas_x',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/data-kepindidikan/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
            // Jika form disubmit untuk update
            if (isset($_POST['submit'])) {
                // Validasi dan sanitasi input dari form
                $kelas = sanitize_text_field($_POST['kelas']);
                $jurusan = sanitize_text_field($_POST['jurusan']);
                $murid_lk = intval($_POST['murid_lk']);
                $murid_pr = intval($_POST['murid_pr']);
                $ket = sanitize_text_field($_POST['ket']);


                // Update data ke database
                $wpdb->update(
                    'data_jumlah_murid_sma_1_bintan_kelas_x',
                    array(
                        'kelas' => $kelas,
                        'jurusan' => $jurusan,
                        'murid_lk' => $murid_lk,
                        'murid_pr' => $murid_pr,
                        'ket' => $ket,
                    ),
                    array('id' => $id),  // Syarat untuk update berdasarkan ID
                    array('%s', '%s', '%d', '%d', '%s'),  // Format tipe data
                    array('%d')  // Format untuk ID
                );

                // Redirect setelah data berhasil diupdate
                echo '<script type="text/javascript">
                    alert("Data berhasil diupdate.");
                    window.location.href = "http://websitekecamatanbintantimur.test/data-kepindidikan/";
                </script>';
                exit;
            }

            // Tampilkan form untuk mengedit data
            return '
                <form method="post">
                    <label for="uraian">Kelas:</label>
                    <input type="text" name="kelas" value="' . esc_attr($result->kelas) . '"><br>
                    <label for="uraian">Jurusan:</label>
                    <input type="text" name="jurusan" value="' . esc_attr($result->jurusan) . '"><br>
                    <label for="uraian">Murid Laki-Laki:</label>
                    <input type="number" name="murid_lk" value="' . esc_attr($result->murid_lk) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Murid Perempuan:</label>
                    <input type="number" name="murid_pr" value="' . esc_attr($result->murid_pr) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Keterangan:</label>
                    <input type="text" name="ket" value="' . esc_attr($result->ket) . '"><br>
                    <input type="submit" name="submit" value="Update Data" class="update-button">
                    <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
                </form>';
        } else {
            // Jika data tidak ditemukan
            return '<p>Data tidak ditemukan.</p>';
        }
    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_data_jumlah_murid_sma_1_bintan_kelas_x', 'edit_data_jumlah_murid_sma_1_bintan_kelas_x');


function tambah_data_jumlah_murid_sma_1_bintan_kelas_x() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_jumlah_murid_sma_1_bintan_kelas_x';

    if (isset($_POST['submit'])) {
        $kelas = sanitize_text_field($_POST['kelas']);
        $jurusan = sanitize_text_field($_POST['jurusan']);
        $murid_lk = intval($_POST['murid_lk']);
        $murid_pr = intval($_POST['murid_pr']);
        $ket = sanitize_text_field($_POST['ket']);

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_jumlah_murid_sma_1_bintan_kelas_x',
            array(
                'nomor' => $new_nomor,
                'kelas' => $kelas,
                'jurusan' => $jurusan,
                'murid_lk' => $murid_lk,
                'murid_pr' => $murid_pr,
                'ket' => $ket,
            ),
            array('%d', '%s', '%s', '%d', '%d', '%s')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/data-kepindidikan/";
        </script>';
        exit;
    }

    return '
        <form method="post">
                    <label for="uraian">Kelas:</label>
                    <input type="text" name="kelas" value="' . esc_attr($result->kelas) . '"><br>
                    <label for="uraian">Jurusan:</label>
                    <input type="text" name="jurusan" value="' . esc_attr($result->jurusan) . '"><br>
                    <label for="uraian">Murid Laki-Laki:</label>
                    <input type="number" name="murid_lk" value="' . esc_attr($result->murid_lk) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Murid Perempuan:</label>
                    <input type="number" name="murid_pr" value="' . esc_attr($result->murid_pr) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Keterangan:</label>
                    <input type="text" name="ket" value="' . esc_attr($result->ket) . '"><br>
        <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_jumlah_murid_sma_1_bintan_kelas_x', 'tambah_data_jumlah_murid_sma_1_bintan_kelas_x');



function  data_jumlah_murid_sma_1_bintan_kelas_xi() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_jumlah_murid_sma_1_bintan_kelas_xi';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
            text-align: left;
        }
        .custom-table th{
            font-size: 10px;
        }    
        .custom-table td{
            font-size: 10px;
        }  
        .custom-table th, .custom-table td {
            padding: 12px;
        }
        .custom-table thead {
            background-color: #f2f2f2;
            color: #333;
        }
        .custom-table tr {
            border-bottom: 1px solid #ddd;
        }
        /* Media Query untuk layar kecil */
        @media (max-width: 768px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font */
            }
        }
        @media (max-width: 480px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font lebih kecil */
            }
        }
        </style>';

        $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
        $output .= '<table class="custom-table">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Kelas</th>';
        $output .= '<th style="padding:12px;">Jurusan</th>';
        $output .= '<th style="padding:12px;">Murid LK</th>';
        $output .= '<th style="padding:12px;">Murid PR</th>';
        $output .= '<th style="padding:12px;">Jumlah</th>';
        $output .= '<th style="padding:12px;">Ket</th>';
                                                // Tambahkan kolom "Aksi" jika admin login
                                                if (current_user_can('administrator')) {
                                                    $output .= '<th style="padding:12px;">Aksi</th>';
                                                }
        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->kelas) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->jurusan) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->murid_lk) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->murid_pr) . '</td>';
            $jumlah = $row->murid_lk + $row->murid_pr;
            $output .= '<td style="padding:12px;">' . esc_html($jumlah) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->ket) . '</td>';
            if (current_user_can('administrator')) {
                $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-data-jumlah-murid-sma-negeri-1-bintan-kelas-xi?id=' . $row->id . '">Edit</a></td>';
            }
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }
    if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-data-jumlah-murid-sma-negeri-1-bintan-kelas-xi" class="tambah-button">Tambah Data Jumlah Murid SMA Negeri 1 Bintan Kelas XI</a>';
        $output .= '</div>';
    }
    return $output;
}

function edit_data_jumlah_murid_sma_1_bintan_kelas_xi() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_jumlah_murid_sma_1_bintan_kelas_xi'; // Nama tabel yang benar

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_jumlah_murid_sma_1_bintan_kelas_xi',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/data-kepindidikan/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
            // Jika form disubmit untuk update
            if (isset($_POST['submit'])) {
                // Validasi dan sanitasi input dari form
                $kelas = sanitize_text_field($_POST['kelas']);
                $jurusan = sanitize_text_field($_POST['jurusan']);
                $murid_lk = intval($_POST['murid_lk']);
                $murid_pr = intval($_POST['murid_pr']);
                $ket = sanitize_text_field($_POST['ket']);


                // Update data ke database
                $wpdb->update(
                    'data_jumlah_murid_sma_1_bintan_kelas_xi',
                    array(
                        'kelas' => $kelas,
                        'jurusan' => $jurusan,
                        'murid_lk' => $murid_lk,
                        'murid_pr' => $murid_pr,
                        'ket' => $ket,
                    ),
                    array('id' => $id),  // Syarat untuk update berdasarkan ID
                    array('%s', '%s', '%d', '%d', '%s'),  // Format tipe data
                    array('%d')  // Format untuk ID
                );

                // Redirect setelah data berhasil diupdate
                echo '<script type="text/javascript">
                    alert("Data berhasil diupdate.");
                    window.location.href = "http://websitekecamatanbintantimur.test/data-kepindidikan/";
                </script>';
                exit;
            }

            // Tampilkan form untuk mengedit data
            return '
                <form method="post">
                    <label for="uraian">Kelas:</label>
                    <input type="text" name="kelas" value="' . esc_attr($result->kelas) . '"><br>
                    <label for="uraian">Jurusan:</label>
                    <input type="text" name="jurusan" value="' . esc_attr($result->jurusan) . '"><br>
                    <label for="uraian">Murid Laki-Laki:</label>
                    <input type="number" name="murid_lk" value="' . esc_attr($result->murid_lk) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Murid Perempuan:</label>
                    <input type="number" name="murid_pr" value="' . esc_attr($result->murid_pr) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Keterangan:</label>
                    <input type="text" name="ket" value="' . esc_attr($result->ket) . '"><br>
                    <input type="submit" name="submit" value="Update Data" class="update-button">
                    <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
                </form>';
        } else {
            // Jika data tidak ditemukan
            return '<p>Data tidak ditemukan.</p>';
        }
    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_data_jumlah_murid_sma_1_bintan_kelas_xi', 'edit_data_jumlah_murid_sma_1_bintan_kelas_xi');


function tambah_data_jumlah_murid_sma_1_bintan_kelas_xi() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_jumlah_murid_sma_1_bintan_kelas_xi';

    if (isset($_POST['submit'])) {
        $kelas = sanitize_text_field($_POST['kelas']);
        $jurusan = sanitize_text_field($_POST['jurusan']);
        $murid_lk = intval($_POST['murid_lk']);
        $murid_pr = intval($_POST['murid_pr']);
        $ket = sanitize_text_field($_POST['ket']);

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_jumlah_murid_sma_1_bintan_kelas_xi',
            array(
                'nomor' => $new_nomor,
                'kelas' => $kelas,
                'jurusan' => $jurusan,
                'murid_lk' => $murid_lk,
                'murid_pr' => $murid_pr,
                'ket' => $ket,
            ),
            array('%d', '%s', '%s', '%d', '%d', '%s')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/data-kepindidikan/";
        </script>';
        exit;
    }

    return '
        <form method="post">
                    <label for="uraian">Kelas:</label>
                    <input type="text" name="kelas" value="' . esc_attr($result->kelas) . '"><br>
                    <label for="uraian">Jurusan:</label>
                    <input type="text" name="jurusan" value="' . esc_attr($result->jurusan) . '"><br>
                    <label for="uraian">Murid Laki-Laki:</label>
                    <input type="number" name="murid_lk" value="' . esc_attr($result->murid_lk) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Murid Perempuan:</label>
                    <input type="number" name="murid_pr" value="' . esc_attr($result->murid_pr) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Keterangan:</label>
                    <input type="text" name="ket" value="' . esc_attr($result->ket) . '"><br>
        <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_jumlah_murid_sma_1_bintan_kelas_xi', 'tambah_data_jumlah_murid_sma_1_bintan_kelas_xi');


function  data_jumlah_murid_sma_1_bintan_kelas_xii() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_jumlah_murid_sma_1_bintan_kelas_xii';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
            text-align: left;
        }
        .custom-table th{
            font-size: 10px;
        }    
        .custom-table td{
            font-size: 10px;
        }  
        .custom-table th, .custom-table td {
            padding: 12px;
        }
        .custom-table thead {
            background-color: #f2f2f2;
            color: #333;
        }
        .custom-table tr {
            border-bottom: 1px solid #ddd;
        }
        /* Media Query untuk layar kecil */
        @media (max-width: 768px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font */
            }
        }
        @media (max-width: 480px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font lebih kecil */
            }
        }
        </style>';

        $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
        $output .= '<table class="custom-table">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Kelas</th>';
        $output .= '<th style="padding:12px;">Jurusan</th>';
        $output .= '<th style="padding:12px;">Murid LK</th>';
        $output .= '<th style="padding:12px;">Murid PR</th>';
        $output .= '<th style="padding:12px;">Jumlah</th>';
        $output .= '<th style="padding:12px;">Ket</th>';
                                            // Tambahkan kolom "Aksi" jika admin login
                                            if (current_user_can('administrator')) {
                                                $output .= '<th style="padding:12px;">Aksi</th>';
                                            }
        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->kelas) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->jurusan) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->murid_lk) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->murid_pr) . '</td>';
            $jumlah = $row->murid_lk + $row->murid_pr;
            $output .= '<td style="padding:12px;">' . esc_html($jumlah) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->ket) . '</td>';
            if (current_user_can('administrator')) {
                $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-data-jumlah-murid-sma-negeri-1-bintan-kelas-xii?id=' . $row->id . '">Edit</a></td>';
            }
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }
    if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-data-jumlah-murid-sma-negeri-1-bintan-kelas-xii" class="tambah-button">Tambah Data Jumlah Murid SMA Negeri 1 Bintan Kelas XII</a>';
        $output .= '</div>';
    }

    return $output;
}

function edit_data_jumlah_murid_sma_1_bintan_kelas_xii() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_jumlah_murid_sma_1_bintan_kelas_xii'; // Nama tabel yang benar

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_jumlah_murid_sma_1_bintan_kelas_xii',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/data-kepindidikan/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
            // Jika form disubmit untuk update
            if (isset($_POST['submit'])) {
                // Validasi dan sanitasi input dari form
                $kelas = sanitize_text_field($_POST['kelas']);
                $jurusan = sanitize_text_field($_POST['jurusan']);
                $murid_lk = intval($_POST['murid_lk']);
                $murid_pr = intval($_POST['murid_pr']);
                $ket = sanitize_text_field($_POST['ket']);


                // Update data ke database
                $wpdb->update(
                    'data_jumlah_murid_sma_1_bintan_kelas_xii',
                    array(
                        'kelas' => $kelas,
                        'jurusan' => $jurusan,
                        'murid_lk' => $murid_lk,
                        'murid_pr' => $murid_pr,
                        'ket' => $ket,
                    ),
                    array('id' => $id),  // Syarat untuk update berdasarkan ID
                    array('%s', '%s', '%d', '%d', '%s'),  // Format tipe data
                    array('%d')  // Format untuk ID
                );

                // Redirect setelah data berhasil diupdate
                echo '<script type="text/javascript">
                    alert("Data berhasil diupdate.");
                    window.location.href = "http://websitekecamatanbintantimur.test/data-kepindidikan/";
                </script>';
                exit;
            }

            // Tampilkan form untuk mengedit data
            return '
                <form method="post">
                    <label for="uraian">Kelas:</label>
                    <input type="text" name="kelas" value="' . esc_attr($result->kelas) . '"><br>
                    <label for="uraian">Jurusan:</label>
                    <input type="text" name="jurusan" value="' . esc_attr($result->jurusan) . '"><br>
                    <label for="uraian">Murid Laki-Laki:</label>
                    <input type="number" name="murid_lk" value="' . esc_attr($result->murid_lk) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Murid Perempuan:</label>
                    <input type="number" name="murid_pr" value="' . esc_attr($result->murid_pr) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Keterangan:</label>
                    <input type="text" name="ket" value="' . esc_attr($result->ket) . '"><br>
                    <input type="submit" name="submit" value="Update Data" class="update-button">
                    <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
                </form>';
        } else {
            // Jika data tidak ditemukan
            return '<p>Data tidak ditemukan.</p>';
        }
    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_data_jumlah_murid_sma_1_bintan_kelas_xii', 'edit_data_jumlah_murid_sma_1_bintan_kelas_xii');


function tambah_data_jumlah_murid_sma_1_bintan_kelas_xii() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_jumlah_murid_sma_1_bintan_kelas_xii';

    if (isset($_POST['submit'])) {
        $kelas = sanitize_text_field($_POST['kelas']);
        $jurusan = sanitize_text_field($_POST['jurusan']);
        $murid_lk = intval($_POST['murid_lk']);
        $murid_pr = intval($_POST['murid_pr']);
        $ket = sanitize_text_field($_POST['ket']);

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_jumlah_murid_sma_1_bintan_kelas_xii',
            array(
                'nomor' => $new_nomor,
                'kelas' => $kelas,
                'jurusan' => $jurusan,
                'murid_lk' => $murid_lk,
                'murid_pr' => $murid_pr,
                'ket' => $ket,
            ),
            array('%d', '%s', '%s', '%d', '%d', '%s')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/data-kepindidikan/";
        </script>';
        exit;
    }

    return '
        <form method="post">
                    <label for="uraian">Kelas:</label>
                    <input type="text" name="kelas" value="' . esc_attr($result->kelas) . '"><br>
                    <label for="uraian">Jurusan:</label>
                    <input type="text" name="jurusan" value="' . esc_attr($result->jurusan) . '"><br>
                    <label for="uraian">Murid Laki-Laki:</label>
                    <input type="number" name="murid_lk" value="' . esc_attr($result->murid_lk) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Murid Perempuan:</label>
                    <input type="number" name="murid_pr" value="' . esc_attr($result->murid_pr) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Keterangan:</label>
                    <input type="text" name="ket" value="' . esc_attr($result->ket) . '"><br>
        <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_jumlah_murid_sma_1_bintan_kelas_xii', 'tambah_data_jumlah_murid_sma_1_bintan_kelas_xii');


function  data_jumlah_murid_smk_1_bintan_kelas_x() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_jumlah_murid_smk_1_bintan_kelas_x';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
            text-align: left;
        }
        .custom-table th{
            font-size: 10px;
        }    
        .custom-table td{
            font-size: 10px;
        }  
        .custom-table th, .custom-table td {
            padding: 12px;
        }
        .custom-table thead {
            background-color: #f2f2f2;
            color: #333;
        }
        .custom-table tr {
            border-bottom: 1px solid #ddd;
        }
        /* Media Query untuk layar kecil */
        @media (max-width: 768px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font */
            }
        }
        @media (max-width: 480px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font lebih kecil */
            }
        }
        </style>';

        $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
        $output .= '<table class="custom-table">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Kelas</th>';
        $output .= '<th style="padding:12px;">Jurusan</th>';
        $output .= '<th style="padding:12px;">Murid LK</th>';
        $output .= '<th style="padding:12px;">Murid PR</th>';
        $output .= '<th style="padding:12px;">Jumlah</th>';
        $output .= '<th style="padding:12px;">Ket</th>';
                                    // Tambahkan kolom "Aksi" jika admin login
                                    if (current_user_can('administrator')) {
                                        $output .= '<th style="padding:12px;">Aksi</th>';
                                    }
        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->kelas) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->jurusan) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->murid_lk) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->murid_pr) . '</td>';
            $jumlah = $row->murid_lk + $row->murid_pr;
            $output .= '<td style="padding:12px;">' . esc_html($jumlah) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->ket) . '</td>';
            if (current_user_can('administrator')) {
                $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-data-jumlah-murid-smk-negeri-1-bintan-kelas-x?id=' . $row->id . '">Edit</a></td>';
            }
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }

    if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-data-jumlah-murid-smk-negeri-1-bintan-kelas-x" class="tambah-button">Tambah Data Jumlah Murid SMK Negeri 1 Bintan Kelas X</a>';
        $output .= '</div>';
    }

    return $output;
}

function edit_data_jumlah_murid_smk_1_bintan_kelas_x() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_jumlah_murid_smk_1_bintan_kelas_x'; // Nama tabel yang benar

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_jumlah_murid_smk_1_bintan_kelas_x',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/data-kepindidikan/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
            // Jika form disubmit untuk update
            if (isset($_POST['submit'])) {
                // Validasi dan sanitasi input dari form
                $kelas = sanitize_text_field($_POST['kelas']);
                $jurusan = sanitize_text_field($_POST['jurusan']);
                $murid_lk = intval($_POST['murid_lk']);
                $murid_pr = intval($_POST['murid_pr']);
                $ket = sanitize_text_field($_POST['ket']);


                // Update data ke database
                $wpdb->update(
                    'data_jumlah_murid_smk_1_bintan_kelas_x',
                    array(
                        'kelas' => $kelas,
                        'jurusan' => $jurusan,
                        'murid_lk' => $murid_lk,
                        'murid_pr' => $murid_pr,
                        'ket' => $ket,
                    ),
                    array('id' => $id),  // Syarat untuk update berdasarkan ID
                    array('%s', '%s', '%d', '%d', '%s'),  // Format tipe data
                    array('%d')  // Format untuk ID
                );

                // Redirect setelah data berhasil diupdate
                echo '<script type="text/javascript">
                    alert("Data berhasil diupdate.");
                    window.location.href = "http://websitekecamatanbintantimur.test/data-kepindidikan/";
                </script>';
                exit;
            }

            // Tampilkan form untuk mengedit data
            return '
                <form method="post">
                    <label for="uraian">Kelas:</label>
                    <input type="text" name="kelas" value="' . esc_attr($result->kelas) . '"><br>
                    <label for="uraian">Jurusan:</label>
                    <input type="text" name="jurusan" value="' . esc_attr($result->jurusan) . '"><br>
                    <label for="uraian">Murid Laki-Laki:</label>
                    <input type="number" name="murid_lk" value="' . esc_attr($result->murid_lk) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Murid Perempuan:</label>
                    <input type="number" name="murid_pr" value="' . esc_attr($result->murid_pr) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Keterangan:</label>
                    <input type="text" name="ket" value="' . esc_attr($result->ket) . '"><br>
                    <input type="submit" name="submit" value="Update Data" class="update-button">
                    <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
                </form>';
        } else {
            // Jika data tidak ditemukan
            return '<p>Data tidak ditemukan.</p>';
        }
    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_data_jumlah_murid_smk_1_bintan_kelas_x', 'edit_data_jumlah_murid_smk_1_bintan_kelas_x');


function tambah_data_jumlah_murid_smk_1_bintan_kelas_x() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_jumlah_murid_smk_1_bintan_kelas_x';

    if (isset($_POST['submit'])) {
        $kelas = sanitize_text_field($_POST['kelas']);
        $jurusan = sanitize_text_field($_POST['jurusan']);
        $murid_lk = intval($_POST['murid_lk']);
        $murid_pr = intval($_POST['murid_pr']);
        $ket = sanitize_text_field($_POST['ket']);

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_jumlah_murid_smk_1_bintan_kelas_x',
            array(
                'nomor' => $new_nomor,
                'kelas' => $kelas,
                'jurusan' => $jurusan,
                'murid_lk' => $murid_lk,
                'murid_pr' => $murid_pr,
                'ket' => $ket,
            ),
            array('%d', '%s', '%s', '%d', '%d', '%s')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/data-kepindidikan/";
        </script>';
        exit;
    }

    return '
        <form method="post">
                    <label for="uraian">Kelas:</label>
                    <input type="text" name="kelas" value="' . esc_attr($result->kelas) . '"><br>
                    <label for="uraian">Jurusan:</label>
                    <input type="text" name="jurusan" value="' . esc_attr($result->jurusan) . '"><br>
                    <label for="uraian">Murid Laki-Laki:</label>
                    <input type="number" name="murid_lk" value="' . esc_attr($result->murid_lk) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Murid Perempuan:</label>
                    <input type="number" name="murid_pr" value="' . esc_attr($result->murid_pr) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Keterangan:</label>
                    <input type="text" name="ket" value="' . esc_attr($result->ket) . '"><br>
        <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_jumlah_murid_smk_1_bintan_kelas_x', 'tambah_data_jumlah_murid_smk_1_bintan_kelas_x');

function  data_jumlah_murid_smk_1_bintan_kelas_xi() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_jumlah_murid_smk_1_bintan_kelas_xi';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
            text-align: left;
        }
        .custom-table th{
            font-size: 10px;
        }    
        .custom-table td{
            font-size: 10px;
        }  
        .custom-table th, .custom-table td {
            padding: 12px;
        }
        .custom-table thead {
            background-color: #f2f2f2;
            color: #333;
        }
        .custom-table tr {
            border-bottom: 1px solid #ddd;
        }
        /* Media Query untuk layar kecil */
        @media (max-width: 768px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font */
            }
        }
        @media (max-width: 480px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font lebih kecil */
            }
        }
        </style>';

        $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
        $output .= '<table class="custom-table">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Kelas</th>';
        $output .= '<th style="padding:12px;">Jurusan</th>';
        $output .= '<th style="padding:12px;">Murid LK</th>';
        $output .= '<th style="padding:12px;">Murid PR</th>';
        $output .= '<th style="padding:12px;">Jumlah</th>';
        $output .= '<th style="padding:12px;">Ket</th>';
                                            // Tambahkan kolom "Aksi" jika admin login
                                            if (current_user_can('administrator')) {
                                                $output .= '<th style="padding:12px;">Aksi</th>';
                                            }
        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->kelas) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->jurusan) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->murid_lk) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->murid_pr) . '</td>';
            $jumlah = $row->murid_lk + $row->murid_pr;
            $output .= '<td style="padding:12px;">' . esc_html($jumlah) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->ket) . '</td>';
            if (current_user_can('administrator')) {
                $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-data-jumlah-murid-smk-negeri-1-bintan-kelas-xi?id=' . $row->id . '">Edit</a></td>';
            }
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }

    if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-data-jumlah-murid-smk-negeri-1-bintan-kelas-xi" class="tambah-button">Tambah Data Jumlah Murid SMK Negeri 1 Bintan Kelas XI</a>';
        $output .= '</div>';
    }

    return $output;
}

function edit_data_jumlah_murid_smk_1_bintan_kelas_xi() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_jumlah_murid_smk_1_bintan_kelas_xi'; // Nama tabel yang benar

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_jumlah_murid_smk_1_bintan_kelas_xi',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/data-kepindidikan/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
            // Jika form disubmit untuk update
            if (isset($_POST['submit'])) {
                // Validasi dan sanitasi input dari form
                $kelas = sanitize_text_field($_POST['kelas']);
                $jurusan = sanitize_text_field($_POST['jurusan']);
                $murid_lk = intval($_POST['murid_lk']);
                $murid_pr = intval($_POST['murid_pr']);
                $ket = sanitize_text_field($_POST['ket']);


                // Update data ke database
                $wpdb->update(
                    'data_jumlah_murid_smk_1_bintan_kelas_xi',
                    array(
                        'kelas' => $kelas,
                        'jurusan' => $jurusan,
                        'murid_lk' => $murid_lk,
                        'murid_pr' => $murid_pr,
                        'ket' => $ket,
                    ),
                    array('id' => $id),  // Syarat untuk update berdasarkan ID
                    array('%s', '%s', '%d', '%d', '%s'),  // Format tipe data
                    array('%d')  // Format untuk ID
                );

                // Redirect setelah data berhasil diupdate
                echo '<script type="text/javascript">
                    alert("Data berhasil diupdate.");
                    window.location.href = "http://websitekecamatanbintantimur.test/data-kepindidikan/";
                </script>';
                exit;
            }

            // Tampilkan form untuk mengedit data
            return '
                <form method="post">
                    <label for="uraian">Kelas:</label>
                    <input type="text" name="kelas" value="' . esc_attr($result->kelas) . '"><br>
                    <label for="uraian">Jurusan:</label>
                    <input type="text" name="jurusan" value="' . esc_attr($result->jurusan) . '"><br>
                    <label for="uraian">Murid Laki-Laki:</label>
                    <input type="number" name="murid_lk" value="' . esc_attr($result->murid_lk) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Murid Perempuan:</label>
                    <input type="number" name="murid_pr" value="' . esc_attr($result->murid_pr) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Keterangan:</label>
                    <input type="text" name="ket" value="' . esc_attr($result->ket) . '"><br>
                    <input type="submit" name="submit" value="Update Data" class="update-button">
                    <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
                </form>';
        } else {
            // Jika data tidak ditemukan
            return '<p>Data tidak ditemukan.</p>';
        }
    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_data_jumlah_murid_smk_1_bintan_kelas_xi', 'edit_data_jumlah_murid_smk_1_bintan_kelas_xi');


function tambah_data_jumlah_murid_smk_1_bintan_kelas_xi() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_jumlah_murid_smk_1_bintan_kelas_xi';

    if (isset($_POST['submit'])) {
        $kelas = sanitize_text_field($_POST['kelas']);
        $jurusan = sanitize_text_field($_POST['jurusan']);
        $murid_lk = intval($_POST['murid_lk']);
        $murid_pr = intval($_POST['murid_pr']);
        $ket = sanitize_text_field($_POST['ket']);

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_jumlah_murid_smk_1_bintan_kelas_xi',
            array(
                'nomor' => $new_nomor,
                'kelas' => $kelas,
                'jurusan' => $jurusan,
                'murid_lk' => $murid_lk,
                'murid_pr' => $murid_pr,
                'ket' => $ket,
            ),
            array('%d', '%s', '%s', '%d', '%d', '%s')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/data-kepindidikan/";
        </script>';
        exit;
    }

    return '
        <form method="post">
                    <label for="uraian">Kelas:</label>
                    <input type="text" name="kelas" value="' . esc_attr($result->kelas) . '"><br>
                    <label for="uraian">Jurusan:</label>
                    <input type="text" name="jurusan" value="' . esc_attr($result->jurusan) . '"><br>
                    <label for="uraian">Murid Laki-Laki:</label>
                    <input type="number" name="murid_lk" value="' . esc_attr($result->murid_lk) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Murid Perempuan:</label>
                    <input type="number" name="murid_pr" value="' . esc_attr($result->murid_pr) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Keterangan:</label>
                    <input type="text" name="ket" value="' . esc_attr($result->ket) . '"><br>
        <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_jumlah_murid_smk_1_bintan_kelas_xi', 'tambah_data_jumlah_murid_smk_1_bintan_kelas_xi');


function  data_jumlah_murid_smk_1_bintan_kelas_xii() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_jumlah_murid_smk_1_bintan_kelas_xii';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
            text-align: left;
        }
        .custom-table th{
            font-size: 10px;
        }    
        .custom-table td{
            font-size: 10px;
        }  
        .custom-table th, .custom-table td {
            padding: 12px;
        }
        .custom-table thead {
            background-color: #f2f2f2;
            color: #333;
        }
        .custom-table tr {
            border-bottom: 1px solid #ddd;
        }
        /* Media Query untuk layar kecil */
        @media (max-width: 768px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font */
            }
        }
        @media (max-width: 480px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font lebih kecil */
            }
        }
        </style>';

        $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
        $output .= '<table class="custom-table">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Kelas</th>';
        $output .= '<th style="padding:12px;">Jurusan</th>';
        $output .= '<th style="padding:12px;">Murid LK</th>';
        $output .= '<th style="padding:12px;">Murid PR</th>';
        $output .= '<th style="padding:12px;">Jumlah</th>';
        $output .= '<th style="padding:12px;">Ket</th>';
                                    // Tambahkan kolom "Aksi" jika admin login
                                    if (current_user_can('administrator')) {
                                        $output .= '<th style="padding:12px;">Aksi</th>';
                                    }
        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->kelas) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->jurusan) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->murid_lk) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->murid_pr) . '</td>';
            $jumlah = $row->murid_lk + $row->murid_pr;
            $output .= '<td style="padding:12px;">' . esc_html($jumlah) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->ket) . '</td>';
            
            if (current_user_can('administrator')) {
                $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-data-jumlah-murid-smk-negeri-1-bintan-kelas-xii?id=' . $row->id . '">Edit</a></td>';
            }
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }

    if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-data-jumlah-murid-smk-negeri-1-bintan-kelas-xii" class="tambah-button">Tambah Data Jumlah Murid SMK Negeri 1 Bintan Kelas XII</a>';
        $output .= '</div>';
    }

    return $output;
}

function edit_data_jumlah_murid_smk_1_bintan_kelas_xii() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_jumlah_murid_smk_1_bintan_kelas_xii'; // Nama tabel yang benar

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_jumlah_murid_smk_1_bintan_kelas_xii',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/data-kepindidikan/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
            // Jika form disubmit untuk update
            if (isset($_POST['submit'])) {
                // Validasi dan sanitasi input dari form
                $kelas = sanitize_text_field($_POST['kelas']);
                $jurusan = sanitize_text_field($_POST['jurusan']);
                $murid_lk = intval($_POST['murid_lk']);
                $murid_pr = intval($_POST['murid_pr']);
                $ket = sanitize_text_field($_POST['ket']);


                // Update data ke database
                $wpdb->update(
                    'data_jumlah_murid_smk_1_bintan_kelas_xii',
                    array(
                        'kelas' => $kelas,
                        'jurusan' => $jurusan,
                        'murid_lk' => $murid_lk,
                        'murid_pr' => $murid_pr,
                        'ket' => $ket,
                    ),
                    array('id' => $id),  // Syarat untuk update berdasarkan ID
                    array('%s', '%s', '%d', '%d', '%s'),  // Format tipe data
                    array('%d')  // Format untuk ID
                );

                // Redirect setelah data berhasil diupdate
                echo '<script type="text/javascript">
                    alert("Data berhasil diupdate.");
                    window.location.href = "http://websitekecamatanbintantimur.test/data-kepindidikan/";
                </script>';
                exit;
            }

            // Tampilkan form untuk mengedit data
            return '
                <form method="post">
                    <label for="uraian">Kelas:</label>
                    <input type="text" name="kelas" value="' . esc_attr($result->kelas) . '"><br>
                    <label for="uraian">Jurusan:</label>
                    <input type="text" name="jurusan" value="' . esc_attr($result->jurusan) . '"><br>
                    <label for="uraian">Murid Laki-Laki:</label>
                    <input type="number" name="murid_lk" value="' . esc_attr($result->murid_lk) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Murid Perempuan:</label>
                    <input type="number" name="murid_pr" value="' . esc_attr($result->murid_pr) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Keterangan:</label>
                    <input type="text" name="ket" value="' . esc_attr($result->ket) . '"><br>
                    <input type="submit" name="submit" value="Update Data" class="update-button">
                    <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
                </form>';
        } else {
            // Jika data tidak ditemukan
            return '<p>Data tidak ditemukan.</p>';
        }
    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_data_jumlah_murid_smk_1_bintan_kelas_xii', 'edit_data_jumlah_murid_smk_1_bintan_kelas_xii');


function tambah_data_jumlah_murid_smk_1_bintan_kelas_xii() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_jumlah_murid_smk_1_bintan_kelas_xii';

    if (isset($_POST['submit'])) {
        $kelas = sanitize_text_field($_POST['kelas']);
        $jurusan = sanitize_text_field($_POST['jurusan']);
        $murid_lk = intval($_POST['murid_lk']);
        $murid_pr = intval($_POST['murid_pr']);
        $ket = sanitize_text_field($_POST['ket']);

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_jumlah_murid_smk_1_bintan_kelas_xii',
            array(
                'nomor' => $new_nomor,
                'kelas' => $kelas,
                'jurusan' => $jurusan,
                'murid_lk' => $murid_lk,
                'murid_pr' => $murid_pr,
                'ket' => $ket,
            ),
            array('%d', '%s', '%s', '%d', '%d', '%s')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/data-kepindidikan/";
        </script>';
        exit;
    }

    return '
        <form method="post">
                    <label for="uraian">Kelas:</label>
                    <input type="text" name="kelas" value="' . esc_attr($result->kelas) . '"><br>
                    <label for="uraian">Jurusan:</label>
                    <input type="text" name="jurusan" value="' . esc_attr($result->jurusan) . '"><br>
                    <label for="uraian">Murid Laki-Laki:</label>
                    <input type="number" name="murid_lk" value="' . esc_attr($result->murid_lk) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Murid Perempuan:</label>
                    <input type="number" name="murid_pr" value="' . esc_attr($result->murid_pr) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Keterangan:</label>
                    <input type="text" name="ket" value="' . esc_attr($result->ket) . '"><br>
        <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_jumlah_murid_smk_1_bintan_kelas_xii', 'tambah_data_jumlah_murid_smk_1_bintan_kelas_xii');


function  data_jumlah_murid_man_bintan_kelas_x() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_jumlah_murid_man_bintan_kelas_x';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
            text-align: left;
        }
        .custom-table th{
            font-size: 10px;
        }    
        .custom-table td{
            font-size: 10px;
        }  
        .custom-table th, .custom-table td {
            padding: 12px;
        }
        .custom-table thead {
            background-color: #f2f2f2;
            color: #333;
        }
        .custom-table tr {
            border-bottom: 1px solid #ddd;
        }
        /* Media Query untuk layar kecil */
        @media (max-width: 768px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font */
            }
        }
        @media (max-width: 480px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font lebih kecil */
            }
        }
        </style>';

        $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
        $output .= '<table class="custom-table">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Kelas</th>';
        $output .= '<th style="padding:12px;">Jurusan</th>';
        $output .= '<th style="padding:12px;">Murid LK</th>';
        $output .= '<th style="padding:12px;">Murid PR</th>';
        $output .= '<th style="padding:12px;">Jumlah</th>';
        $output .= '<th style="padding:12px;">Ket</th>';
                                            // Tambahkan kolom "Aksi" jika admin login
                                            if (current_user_can('administrator')) {
                                                $output .= '<th style="padding:12px;">Aksi</th>';
                                            }
        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->kelas) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->jurusan) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->murid_lk) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->murid_pr) . '</td>';
            $jumlah = $row->murid_lk + $row->murid_pr;
            $output .= '<td style="padding:12px;">' . esc_html($jumlah) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->ket) . '</td>';
            if (current_user_can('administrator')) {
                $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-data-jumlah-murid-man-bintan-kelas-x?id=' . $row->id . '">Edit</a></td>';
            }
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }
    if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-data-jumlah-murid-man-bintan-kelas-x" class="tambah-button">Tambah Data Jumlah Murid MAN Bintan Kelas X</a>';
        $output .= '</div>';
    }

    return $output;
}

function edit_data_jumlah_murid_man_bintan_kelas_x() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_jumlah_murid_man_bintan_kelas_x'; // Nama tabel yang benar

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_jumlah_murid_man_bintan_kelas_x',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/data-kepindidikan/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
            // Jika form disubmit untuk update
            if (isset($_POST['submit'])) {
                // Validasi dan sanitasi input dari form
                $kelas = sanitize_text_field($_POST['kelas']);
                $jurusan = sanitize_text_field($_POST['jurusan']);
                $murid_lk = intval($_POST['murid_lk']);
                $murid_pr = intval($_POST['murid_pr']);
                $ket = sanitize_text_field($_POST['ket']);


                // Update data ke database
                $wpdb->update(
                    'data_jumlah_murid_man_bintan_kelas_x',
                    array(
                        'kelas' => $kelas,
                        'jurusan' => $jurusan,
                        'murid_lk' => $murid_lk,
                        'murid_pr' => $murid_pr,
                        'ket' => $ket,
                    ),
                    array('id' => $id),  // Syarat untuk update berdasarkan ID
                    array('%s', '%s', '%d', '%d', '%s'),  // Format tipe data
                    array('%d')  // Format untuk ID
                );

                // Redirect setelah data berhasil diupdate
                echo '<script type="text/javascript">
                    alert("Data berhasil diupdate.");
                    window.location.href = "http://websitekecamatanbintantimur.test/data-kepindidikan/";
                </script>';
                exit;
            }

            // Tampilkan form untuk mengedit data
            return '
                <form method="post">
                    <label for="uraian">Kelas:</label>
                    <input type="text" name="kelas" value="' . esc_attr($result->kelas) . '"><br>
                    <label for="uraian">Jurusan:</label>
                    <input type="text" name="jurusan" value="' . esc_attr($result->jurusan) . '"><br>
                    <label for="uraian">Murid Laki-Laki:</label>
                    <input type="number" name="murid_lk" value="' . esc_attr($result->murid_lk) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Murid Perempuan:</label>
                    <input type="number" name="murid_pr" value="' . esc_attr($result->murid_pr) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Keterangan:</label>
                    <input type="text" name="ket" value="' . esc_attr($result->ket) . '"><br>
                    <input type="submit" name="submit" value="Update Data" class="update-button">
                    <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
                </form>';
        } else {
            // Jika data tidak ditemukan
            return '<p>Data tidak ditemukan.</p>';
        }
    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_data_jumlah_murid_man_bintan_kelas_x', 'edit_data_jumlah_murid_man_bintan_kelas_x');


function tambah_data_jumlah_murid_man_bintan_kelas_x() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_jumlah_murid_man_bintan_kelas_x';

    if (isset($_POST['submit'])) {
        $kelas = sanitize_text_field($_POST['kelas']);
        $jurusan = sanitize_text_field($_POST['jurusan']);
        $murid_lk = intval($_POST['murid_lk']);
        $murid_pr = intval($_POST['murid_pr']);
        $ket = sanitize_text_field($_POST['ket']);

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_jumlah_murid_man_bintan_kelas_x',
            array(
                'nomor' => $new_nomor,
                'kelas' => $kelas,
                'jurusan' => $jurusan,
                'murid_lk' => $murid_lk,
                'murid_pr' => $murid_pr,
                'ket' => $ket,
            ),
            array('%d', '%s', '%s', '%d', '%d', '%s')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/data-kepindidikan/";
        </script>';
        exit;
    }

    return '
        <form method="post">
                    <label for="uraian">Kelas:</label>
                    <input type="text" name="kelas" value="' . esc_attr($result->kelas) . '"><br>
                    <label for="uraian">Jurusan:</label>
                    <input type="text" name="jurusan" value="' . esc_attr($result->jurusan) . '"><br>
                    <label for="uraian">Murid Laki-Laki:</label>
                    <input type="number" name="murid_lk" value="' . esc_attr($result->murid_lk) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Murid Perempuan:</label>
                    <input type="number" name="murid_pr" value="' . esc_attr($result->murid_pr) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Keterangan:</label>
                    <input type="text" name="ket" value="' . esc_attr($result->ket) . '"><br>
        <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_jumlah_murid_man_bintan_kelas_x', 'tambah_data_jumlah_murid_man_bintan_kelas_x');

function  data_jumlah_murid_man_bintan_kelas_xi() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_jumlah_murid_man_bintan_kelas_xi';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
            text-align: left;
        }
        .custom-table th{
            font-size: 10px;
        }    
        .custom-table td{
            font-size: 10px;
        }  
        .custom-table th, .custom-table td {
            padding: 12px;
        }
        .custom-table thead {
            background-color: #f2f2f2;
            color: #333;
        }
        .custom-table tr {
            border-bottom: 1px solid #ddd;
        }
        /* Media Query untuk layar kecil */
        @media (max-width: 768px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font */
            }
        }
        @media (max-width: 480px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font lebih kecil */
            }
        }
        </style>';

        $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
        $output .= '<table class="custom-table">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Kelas</th>';
        $output .= '<th style="padding:12px;">Jurusan</th>';
        $output .= '<th style="padding:12px;">Murid LK</th>';
        $output .= '<th style="padding:12px;">Murid PR</th>';
        $output .= '<th style="padding:12px;">Jumlah</th>';
        $output .= '<th style="padding:12px;">Ket</th>';
                                            // Tambahkan kolom "Aksi" jika admin login
                                            if (current_user_can('administrator')) {
                                                $output .= '<th style="padding:12px;">Aksi</th>';
                                            }
        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->kelas) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->jurusan) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->murid_lk) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->murid_pr) . '</td>';
            $jumlah = $row->murid_lk + $row->murid_pr;
            $output .= '<td style="padding:12px;">' . esc_html($jumlah) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->ket) . '</td>';
            if (current_user_can('administrator')) {
                $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-data-jumlah-murid-man-bintan-kelas-xi?id=' . $row->id . '">Edit</a></td>';
            }
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }

    if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-data-jumlah-murid-man-bintan-kelas-xi" class="tambah-button">Tambah Data Jumlah Murid MAN Bintan Kelas XI</a>';
        $output .= '</div>';
    }

    return $output;
}

function edit_data_jumlah_murid_man_bintan_kelas_xi() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_jumlah_murid_man_bintan_kelas_xi'; // Nama tabel yang benar

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_jumlah_murid_man_bintan_kelas_xi',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/data-kepindidikan/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
            // Jika form disubmit untuk update
            if (isset($_POST['submit'])) {
                // Validasi dan sanitasi input dari form
                $kelas = sanitize_text_field($_POST['kelas']);
                $jurusan = sanitize_text_field($_POST['jurusan']);
                $murid_lk = intval($_POST['murid_lk']);
                $murid_pr = intval($_POST['murid_pr']);
                $ket = sanitize_text_field($_POST['ket']);


                // Update data ke database
                $wpdb->update(
                    'data_jumlah_murid_man_bintan_kelas_xi',
                    array(
                        'kelas' => $kelas,
                        'jurusan' => $jurusan,
                        'murid_lk' => $murid_lk,
                        'murid_pr' => $murid_pr,
                        'ket' => $ket,
                    ),
                    array('id' => $id),  // Syarat untuk update berdasarkan ID
                    array('%s', '%s', '%d', '%d', '%s'),  // Format tipe data
                    array('%d')  // Format untuk ID
                );

                // Redirect setelah data berhasil diupdate
                echo '<script type="text/javascript">
                    alert("Data berhasil diupdate.");
                    window.location.href = "http://websitekecamatanbintantimur.test/data-kepindidikan/";
                </script>';
                exit;
            }

            // Tampilkan form untuk mengedit data
            return '
                <form method="post">
                    <label for="uraian">Kelas:</label>
                    <input type="text" name="kelas" value="' . esc_attr($result->kelas) . '"><br>
                    <label for="uraian">Jurusan:</label>
                    <input type="text" name="jurusan" value="' . esc_attr($result->jurusan) . '"><br>
                    <label for="uraian">Murid Laki-Laki:</label>
                    <input type="number" name="murid_lk" value="' . esc_attr($result->murid_lk) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Murid Perempuan:</label>
                    <input type="number" name="murid_pr" value="' . esc_attr($result->murid_pr) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Keterangan:</label>
                    <input type="text" name="ket" value="' . esc_attr($result->ket) . '"><br>
                    <input type="submit" name="submit" value="Update Data" class="update-button">
                    <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
                </form>';
        } else {
            // Jika data tidak ditemukan
            return '<p>Data tidak ditemukan.</p>';
        }
    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_data_jumlah_murid_man_bintan_kelas_xi', 'edit_data_jumlah_murid_man_bintan_kelas_xi');


function tambah_data_jumlah_murid_man_bintan_kelas_xi() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_jumlah_murid_man_bintan_kelas_xi';

    if (isset($_POST['submit'])) {
        $kelas = sanitize_text_field($_POST['kelas']);
        $jurusan = sanitize_text_field($_POST['jurusan']);
        $murid_lk = intval($_POST['murid_lk']);
        $murid_pr = intval($_POST['murid_pr']);
        $ket = sanitize_text_field($_POST['ket']);

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_jumlah_murid_man_bintan_kelas_xi',
            array(
                'nomor' => $new_nomor,
                'kelas' => $kelas,
                'jurusan' => $jurusan,
                'murid_lk' => $murid_lk,
                'murid_pr' => $murid_pr,
                'ket' => $ket,
            ),
            array('%d', '%s', '%s', '%d', '%d', '%s')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/data-kepindidikan/";
        </script>';
        exit;
    }

    return '
        <form method="post">
                    <label for="uraian">Kelas:</label>
                    <input type="text" name="kelas" value="' . esc_attr($result->kelas) . '"><br>
                    <label for="uraian">Jurusan:</label>
                    <input type="text" name="jurusan" value="' . esc_attr($result->jurusan) . '"><br>
                    <label for="uraian">Murid Laki-Laki:</label>
                    <input type="number" name="murid_lk" value="' . esc_attr($result->murid_lk) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Murid Perempuan:</label>
                    <input type="number" name="murid_pr" value="' . esc_attr($result->murid_pr) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Keterangan:</label>
                    <input type="text" name="ket" value="' . esc_attr($result->ket) . '"><br>
        <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_jumlah_murid_man_bintan_kelas_xi', 'tambah_data_jumlah_murid_man_bintan_kelas_xi');


function  data_jumlah_murid_man_bintan_kelas_xii() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_jumlah_murid_man_bintan_kelas_xii';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
            text-align: left;
        }
        .custom-table th{
            font-size: 10px;
        }    
        .custom-table td{
            font-size: 10px;
        }  
        .custom-table th, .custom-table td {
            padding: 12px;
        }
        .custom-table thead {
            background-color: #f2f2f2;
            color: #333;
        }
        .custom-table tr {
            border-bottom: 1px solid #ddd;
        }
        /* Media Query untuk layar kecil */
        @media (max-width: 768px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font */
            }
        }
        @media (max-width: 480px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font lebih kecil */
            }
        }
        </style>';

        $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
        $output .= '<table class="custom-table">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Kelas</th>';
        $output .= '<th style="padding:12px;">Jurusan</th>';
        $output .= '<th style="padding:12px;">Murid LK</th>';
        $output .= '<th style="padding:12px;">Murid PR</th>';
        $output .= '<th style="padding:12px;">Jumlah</th>';
        $output .= '<th style="padding:12px;">Ket</th>';
                                            // Tambahkan kolom "Aksi" jika admin login
                                            if (current_user_can('administrator')) {
                                                $output .= '<th style="padding:12px;">Aksi</th>';
                                            }
        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->kelas) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->jurusan) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->murid_lk) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->murid_pr) . '</td>';
            $jumlah = $row->murid_lk + $row->murid_pr;
            $output .= '<td style="padding:12px;">' . esc_html($jumlah) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->ket) . '</td>';
            if (current_user_can('administrator')) {
                $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-data-jumlah-murid-man-bintan-kelas-xii?id=' . $row->id . '">Edit</a></td>';
            }
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }
    if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-data-jumlah-murid-man-bintan-kelas-xii" class="tambah-button">Tambah Data Jumlah Murid MAN Bintan Kelas XII</a>';
        $output .= '</div>';
    }

    return $output;
}

function edit_data_jumlah_murid_man_bintan_kelas_xii() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_jumlah_murid_man_bintan_kelas_xii'; // Nama tabel yang benar

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_jumlah_murid_man_bintan_kelas_xii',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/data-kepindidikan/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
            // Jika form disubmit untuk update
            if (isset($_POST['submit'])) {
                // Validasi dan sanitasi input dari form
                $kelas = sanitize_text_field($_POST['kelas']);
                $jurusan = sanitize_text_field($_POST['jurusan']);
                $murid_lk = intval($_POST['murid_lk']);
                $murid_pr = intval($_POST['murid_pr']);
                $ket = sanitize_text_field($_POST['ket']);


                // Update data ke database
                $wpdb->update(
                    'data_jumlah_murid_man_bintan_kelas_xii',
                    array(
                        'kelas' => $kelas,
                        'jurusan' => $jurusan,
                        'murid_lk' => $murid_lk,
                        'murid_pr' => $murid_pr,
                        'ket' => $ket,
                    ),
                    array('id' => $id),  // Syarat untuk update berdasarkan ID
                    array('%s', '%s', '%d', '%d', '%s'),  // Format tipe data
                    array('%d')  // Format untuk ID
                );

                // Redirect setelah data berhasil diupdate
                echo '<script type="text/javascript">
                    alert("Data berhasil diupdate.");
                    window.location.href = "http://websitekecamatanbintantimur.test/data-kepindidikan/";
                </script>';
                exit;
            }

            // Tampilkan form untuk mengedit data
            return '
                <form method="post">
                    <label for="uraian">Kelas:</label>
                    <input type="text" name="kelas" value="' . esc_attr($result->kelas) . '"><br>
                    <label for="uraian">Jurusan:</label>
                    <input type="text" name="jurusan" value="' . esc_attr($result->jurusan) . '"><br>
                    <label for="uraian">Murid Laki-Laki:</label>
                    <input type="number" name="murid_lk" value="' . esc_attr($result->murid_lk) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Murid Perempuan:</label>
                    <input type="number" name="murid_pr" value="' . esc_attr($result->murid_pr) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Keterangan:</label>
                    <input type="text" name="ket" value="' . esc_attr($result->ket) . '"><br>
                    <input type="submit" name="submit" value="Update Data" class="update-button">
                    <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
                </form>';
        } else {
            // Jika data tidak ditemukan
            return '<p>Data tidak ditemukan.</p>';
        }
    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_data_jumlah_murid_man_bintan_kelas_xii', 'edit_data_jumlah_murid_man_bintan_kelas_xii');


function tambah_data_jumlah_murid_man_bintan_kelas_xii() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_jumlah_murid_man_bintan_kelas_xii';

    if (isset($_POST['submit'])) {
        $kelas = sanitize_text_field($_POST['kelas']);
        $jurusan = sanitize_text_field($_POST['jurusan']);
        $murid_lk = intval($_POST['murid_lk']);
        $murid_pr = intval($_POST['murid_pr']);
        $ket = sanitize_text_field($_POST['ket']);

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_jumlah_murid_man_bintan_kelas_xii',
            array(
                'nomor' => $new_nomor,
                'kelas' => $kelas,
                'jurusan' => $jurusan,
                'murid_lk' => $murid_lk,
                'murid_pr' => $murid_pr,
                'ket' => $ket,
            ),
            array('%d', '%s', '%s', '%d', '%d', '%s')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/data-kepindidikan/";
        </script>';
        exit;
    }

    return '
        <form method="post">
                    <label for="uraian">Kelas:</label>
                    <input type="text" name="kelas" value="' . esc_attr($result->kelas) . '"><br>
                    <label for="uraian">Jurusan:</label>
                    <input type="text" name="jurusan" value="' . esc_attr($result->jurusan) . '"><br>
                    <label for="uraian">Murid Laki-Laki:</label>
                    <input type="number" name="murid_lk" value="' . esc_attr($result->murid_lk) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Murid Perempuan:</label>
                    <input type="number" name="murid_pr" value="' . esc_attr($result->murid_pr) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Keterangan:</label>
                    <input type="text" name="ket" value="' . esc_attr($result->ket) . '"><br>
        <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_jumlah_murid_man_bintan_kelas_xii', 'tambah_data_jumlah_murid_man_bintan_kelas_xii');


function  data_jumlah_pengajar_tk_sd_mi_smp_mts() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_jumlah_pengajar_tk_sd_mi_smp_mts';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
            text-align: left;
        }
        .custom-table th{
            font-size: 10px;
        }    
        .custom-table td{
            font-size: 10px;
        }  
        .custom-table th, .custom-table td {
            padding: 12px;
        }
        .custom-table thead {
            background-color: #f2f2f2;
            color: #333;
        }
        .custom-table tr {
            border-bottom: 1px solid #ddd;
        }
        /* Media Query untuk layar kecil */
        @media (max-width: 768px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font */
            }
        }
        @media (max-width: 480px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font lebih kecil */
            }
        }
        </style>';

        $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
        $output .= '<table class="custom-table">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Guru Pengajar</th>';
        $output .= '<th style="padding:12px;">Guru LK</th>';
        $output .= '<th style="padding:12px;">Guru PR</th>';
        $output .= '<th style="padding:12px;">Jumlah</th>';
        $output .= '<th style="padding:12px;">Ket</th>';
        // Tambahkan kolom "Aksi" jika admin login
        if (current_user_can('administrator')) {
            $output .= '<th style="padding:12px;">Aksi</th>';
        }
        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->guru_pengajar) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->guru_lk) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->guru_pr) . '</td>';
            $jumlah = $row->guru_lk + $row->guru_pr;
            $output .= '<td style="padding:12px;">' . esc_html($jumlah) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->ket) . '</td>';
                                    // Jika admin login, tampilkan tombol Edit
                                    if (current_user_can('administrator')) {
                                        $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-data-jumlah-guru-pengajar-tk-paud-sd-mi-smp-mts?id=' . $row->id . '">Edit</a></td>';
                        
                                    }
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }

    if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-data-jumlah-guru-pengajar-tk-paud-sd-mi-smp-mts" class="tambah-button">Tambah Data Jumlah Guru Pengajar TK/PAUD, SD,MI, SMP, MTs</a>';
        $output .= '</div>';
    }

    return $output;
}

function edit_data_jumlah_pengajar_tk_sd_mi_smp_mts() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_jumlah_pengajar_tk_sd_mi_smp_mts'; // Nama tabel yang benar

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_jumlah_pengajar_tk_sd_mi_smp_mts',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/data-kepindidikan/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
            // Jika form disubmit untuk update
            if (isset($_POST['submit'])) {
                // Validasi dan sanitasi input dari form
                $guru_pengajar = sanitize_text_field($_POST['guru_pengajar']);
                $guru_lk = intval($_POST['guru_lk']);
                $guru_pr = intval($_POST['guru_pr']);
                $ket = sanitize_text_field($_POST['ket']);


                // Update data ke database
                $wpdb->update(
                    'data_jumlah_pengajar_tk_sd_mi_smp_mts',
                    array(
                        'guru_pengajar' => $guru_pengajar,
                        'guru_lk' => $guru_lk,
                        'guru_pr' => $guru_pr,
                        'ket' => $ket,
                    ),
                    array('id' => $id),  // Syarat untuk update berdasarkan ID
                    array('%s', '%d', '%d', '%s'),  // Format tipe data
                    array('%d')  // Format untuk ID
                );

                // Redirect setelah data berhasil diupdate
                echo '<script type="text/javascript">
                    alert("Data berhasil diupdate.");
                    window.location.href = "http://websitekecamatanbintantimur.test/data-kepindidikan/";
                </script>';
                exit;
            }

            // Tampilkan form untuk mengedit data
            return '
                <form method="post">
                    <label for="uraian">Guru Pengajar:</label>
                    <input type="text" name="guru_pengajar" value="' . esc_attr($result->guru_pengajar) . '"><br>
                    <label for="uraian">Jumlah Guru Laki-Laki:</label>
                    <input type="number" name="guru_lk" value="' . esc_attr($result->guru_lk) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Guru Perempuan:</label>
                    <input type="number" name="guru_pr" value="' . esc_attr($result->guru_pr) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Keterangan:</label>
                    <input type="text" name="ket" value="' . esc_attr($result->ket) . '"><br>
                    <input type="submit" name="submit" value="Update Data" class="update-button">
                    <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
                </form>';
        } else {
            // Jika data tidak ditemukan
            return '<p>Data tidak ditemukan.</p>';
        }
    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_data_jumlah_pengajar_tk_sd_mi_smp_mts', 'edit_data_jumlah_pengajar_tk_sd_mi_smp_mts');


function tambah_data_jumlah_pengajar_tk_sd_mi_smp_mts() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_jumlah_pengajar_tk_sd_mi_smp_mts';

    if (isset($_POST['submit'])) {
        $guru_pengajar = sanitize_text_field($_POST['guru_pengajar']);
        $guru_lk = intval($_POST['guru_lk']);
        $guru_pr = intval($_POST['guru_pr']);
        $ket = sanitize_text_field($_POST['ket']);

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_jumlah_pengajar_tk_sd_mi_smp_mts',
            array(
                'nomor' => $new_nomor,
                'guru_pengajar' => $guru_pengajar,
                'guru_lk' => $guru_lk,
                'guru_pr' => $guru_pr,
                'ket' => $ket,
            ),
            array('%d', '%s', '%d', '%d', '%s')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/data-kepindidikan/";
        </script>';
        exit;
    }

    return '
        <form method="post">
                <label for="uraian">Guru Pengajar:</label>
                    <input type="text" name="guru_pengajar" value="' . esc_attr($result->guru_pengajar) . '"><br>
                    <label for="uraian">Jumlah Guru Laki-Laki:</label>
                    <input type="number" name="guru_lk" value="' . esc_attr($result->guru_lk) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Guru Perempuan:</label>
                    <input type="number" name="guru_pr" value="' . esc_attr($result->guru_pr) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Keterangan:</label>
                    <input type="text" name="ket" value="' . esc_attr($result->ket) . '"><br>
        <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_jumlah_pengajar_tk_sd_mi_smp_mts', 'tambah_data_jumlah_pengajar_tk_sd_mi_smp_mts');


function  data_jumlah_pengajar_sma_smk_man() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_jumlah_pengajar_sma_smk_man';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
            text-align: left;
        }
        .custom-table th{
            font-size: 10px;
        }    
        .custom-table td{
            font-size: 10px;
        }  
        .custom-table th, .custom-table td {
            padding: 12px;
        }
        .custom-table thead {
            background-color: #f2f2f2;
            color: #333;
        }
        .custom-table tr {
            border-bottom: 1px solid #ddd;
        }
        /* Media Query untuk layar kecil */
        @media (max-width: 768px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font */
            }
        }
        @media (max-width: 480px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font lebih kecil */
            }
        }
        </style>';

        $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
        $output .= '<table class="custom-table">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Guru Pengajar</th>';
        $output .= '<th style="padding:12px;">Guru LK</th>';
        $output .= '<th style="padding:12px;">Guru PR</th>';
        $output .= '<th style="padding:12px;">Jumlah</th>';
        $output .= '<th style="padding:12px;">Ket</th>';
                                    // Tambahkan kolom "Aksi" jika admin login
                                    if (current_user_can('administrator')) {
                                        $output .= '<th style="padding:12px;">Aksi</th>';
                                    }
        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->guru_pengajar) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->guru_lk) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->guru_pr) . '</td>';
            $jumlah = $row->guru_lk + $row->guru_pr;
            $output .= '<td style="padding:12px;">' . esc_html($jumlah) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->ket) . '</td>';
                                    // Jika admin login, tampilkan tombol Edit
                                    if (current_user_can('administrator')) {
                                        $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-data-jumlah-guru-pengajar-sma-1-smk-1-man?id=' . $row->id . '">Edit</a></td>';
                        
                                    }
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }

    if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-data-jumlah-guru-pengajar-sma-1-smk-1-man" class="tambah-button">Tambah Data Jumlah Guru Pengajar SMA NEGERI 1, SMK NEGERI 1, MAN</a>';
        $output .= '</div>';
    }

    return $output;
}

function edit_data_jumlah_pengajar_sma_smk_man() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_jumlah_pengajar_sma_smk_man'; // Nama tabel yang benar

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_jumlah_pengajar_sma_smk_man',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/data-kepindidikan/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
            // Jika form disubmit untuk update
            if (isset($_POST['submit'])) {
                // Validasi dan sanitasi input dari form
                $guru_pengajar = sanitize_text_field($_POST['guru_pengajar']);
                $guru_lk = intval($_POST['guru_lk']);
                $guru_pr = intval($_POST['guru_pr']);
                $ket = sanitize_text_field($_POST['ket']);


                // Update data ke database
                $wpdb->update(
                    'data_jumlah_pengajar_sma_smk_man',
                    array(
                        'guru_pengajar' => $guru_pengajar,
                        'guru_lk' => $guru_lk,
                        'guru_pr' => $guru_pr,
                        'ket' => $ket,
                    ),
                    array('id' => $id),  // Syarat untuk update berdasarkan ID
                    array('%s', '%d', '%d', '%s'),  // Format tipe data
                    array('%d')  // Format untuk ID
                );

                // Redirect setelah data berhasil diupdate
                echo '<script type="text/javascript">
                    alert("Data berhasil diupdate.");
                    window.location.href = "http://websitekecamatanbintantimur.test/data-kepindidikan/";
                </script>';
                exit;
            }

            // Tampilkan form untuk mengedit data
            return '
                <form method="post">
                    <label for="uraian">Guru Pengajar:</label>
                    <input type="text" name="guru_pengajar" value="' . esc_attr($result->guru_pengajar) . '"><br>
                    <label for="uraian">Jumlah Guru Laki-Laki:</label>
                    <input type="number" name="guru_lk" value="' . esc_attr($result->guru_lk) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Guru Perempuan:</label>
                    <input type="number" name="guru_pr" value="' . esc_attr($result->guru_pr) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Keterangan:</label>
                    <input type="text" name="ket" value="' . esc_attr($result->ket) . '"><br>
                    <input type="submit" name="submit" value="Update Data" class="update-button">
                    <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
                </form>';
        } else {
            // Jika data tidak ditemukan
            return '<p>Data tidak ditemukan.</p>';
        }
    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_data_jumlah_pengajar_sma_smk_man', 'edit_data_jumlah_pengajar_sma_smk_man');


function tambah_data_jumlah_pengajar_sma_smk_man() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_jumlah_pengajar_sma_smk_man';

    if (isset($_POST['submit'])) {
        $guru_pengajar = sanitize_text_field($_POST['guru_pengajar']);
        $guru_lk = intval($_POST['guru_lk']);
        $guru_pr = intval($_POST['guru_pr']);
        $ket = sanitize_text_field($_POST['ket']);

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_jumlah_pengajar_sma_smk_man',
            array(
                'nomor' => $new_nomor,
                'guru_pengajar' => $guru_pengajar,
                'guru_lk' => $guru_lk,
                'guru_pr' => $guru_pr,
                'ket' => $ket,
            ),
            array('%d', '%s', '%d', '%d', '%s')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/data-kepindidikan/";
        </script>';
        exit;
    }

    return '
        <form method="post">
                <label for="uraian">Guru Pengajar:</label>
                    <input type="text" name="guru_pengajar" value="' . esc_attr($result->guru_pengajar) . '"><br>
                    <label for="uraian">Jumlah Guru Laki-Laki:</label>
                    <input type="number" name="guru_lk" value="' . esc_attr($result->guru_lk) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Guru Perempuan:</label>
                    <input type="number" name="guru_pr" value="' . esc_attr($result->guru_pr) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Keterangan:</label>
                    <input type="text" name="ket" value="' . esc_attr($result->ket) . '"><br>
        <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_jumlah_pengajar_sma_smk_man', 'tambah_data_jumlah_pengajar_sma_smk_man');


function  data_relasi_pembangunan_kelurahan_kijang_kota() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_relasi_pembangunan_kelurahan_kijang_kota';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
            text-align: left;
        }
        .custom-table th{
            font-size: 10px;
        }    
        .custom-table td{
            font-size: 10px;
        }  
        .custom-table th, .custom-table td {
            padding: 12px;
        }
        .custom-table thead {
            background-color: #f2f2f2;
            color: #333;
        }
        .custom-table tr {
            border-bottom: 1px solid #ddd;
        }
        /* Media Query untuk layar kecil */
        @media (max-width: 768px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font */
            }
        }
        @media (max-width: 480px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font lebih kecil */
            }
        }
        </style>';

        $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
        $output .= '<table class="custom-table">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Jenis Kegiatan</th>';
        $output .= '<th style="padding:12px;">Ket</th>';
                                                // Tambahkan kolom "Aksi" jika admin login
                                                if (current_user_can('administrator')) {
                                                    $output .= '<th style="padding:12px;">Aksi</th>';
                                                }
        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->jenis_kegiatan) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->ket) . '%</td>';
                                    // Jika admin login, tampilkan tombol Edit
                                    if (current_user_can('administrator')) {
                                        $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-data-relasi-pembangunan-kijang-kota?id=' . $row->id . '">Edit</a></td>';
                        
                                    }
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }
    if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-data-relasi-pembangunan-kijang-kota" class="tambah-button">Tambah Data Realisasi Pembangunan Kelurahan Kijang Kota</a>';
        $output .= '</div>';
    }
    return $output;
}

function edit_data_relasi_pembangunan_kelurahan_kijang_kota() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_relasi_pembangunan_kelurahan_kijang_kota'; // Nama tabel yang benar

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_relasi_pembangunan_kelurahan_kijang_kota',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/data-potensi-dan-informasi/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
            // Jika form disubmit untuk update
            if (isset($_POST['submit'])) {
                // Validasi dan sanitasi input dari form
                $jenis_kegiatan = sanitize_text_field($_POST['jenis_kegiatan']);
                $ket = intval($_POST['ket']);


                // Update data ke database
                $wpdb->update(
                    'data_relasi_pembangunan_kelurahan_kijang_kota',
                    array(
                        'jenis_kegiatan' => $jenis_kegiatan,
                        'ket' => $ket,
                    ),
                    array('id' => $id),  // Syarat untuk update berdasarkan ID
                    array('%s', '%d'),  // Format tipe data
                    array('%d')  // Format untuk ID
                );

                // Redirect setelah data berhasil diupdate
                echo '<script type="text/javascript">
                    alert("Data berhasil diupdate.");
                    window.location.href = "http://websitekecamatanbintantimur.test/data-potensi-dan-informasi/";
                </script>';
                exit;
            }

            // Tampilkan form untuk mengedit data
            return '
                <form method="post">
                    <label for="uraian">Jenis Kegiatan:</label>
                    <input type="text" name="jenis_kegiatan" value="' . esc_attr($result->jenis_kegiatan) . '"><br>
                    <label for="uraian">Keterangan:</label>
                    <input type="number" name="ket" value="' . esc_attr($result->ket) . '" inputmode="numeric" pattern="\d*"><br>
                    <input type="submit" name="submit" value="Update Data" class="update-button">
                    <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
                </form>';
        } else {
            // Jika data tidak ditemukan
            return '<p>Data tidak ditemukan.</p>';
        }
    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_data_relasi_pembangunan_kelurahan_kijang_kota', 'edit_data_relasi_pembangunan_kelurahan_kijang_kota');


function tambah_data_relasi_pembangunan_kelurahan_kijang_kota() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_relasi_pembangunan_kelurahan_kijang_kota';

    if (isset($_POST['submit'])) {
        $jenis_kegiatan = sanitize_text_field($_POST['jenis_kegiatan']);
        $ket = intval($_POST['ket']);

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_relasi_pembangunan_kelurahan_kijang_kota',
            array(
                'nomor' => $new_nomor,
                'jenis_kegiatan' => $jenis_kegiatan,
                'ket' => $ket,
            ),
            array('%d', '%s', '%d')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/data-potensi-dan-informasi/";
        </script>';
        exit;
    }

    return '
        <form method="post">
                <label for="uraian">Jenis Kegiatan:</label>
                    <input type="text" name="jenis_kegiatan" value="' . esc_attr($result->jenis_kegiatan) . '"><br>
                    <label for="uraian">Keterangan:</label>
                    <input type="number" name="ket" value="' . esc_attr($result->ket) . '" inputmode="numeric" pattern="\d*"><br>
        <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_relasi_pembangunan_kelurahan_kijang_kota', 'tambah_data_relasi_pembangunan_kelurahan_kijang_kota');


function  data_relasi_pembangunan_kelurahan_sungai_enam() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_relasi_pembangunan_kelurahan_sungai_enam';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
            text-align: left;
        }
        .custom-table th{
            font-size: 10px;
        }    
        .custom-table td{
            font-size: 10px;
        }  
        .custom-table th, .custom-table td {
            padding: 12px;
        }
        .custom-table thead {
            background-color: #f2f2f2;
            color: #333;
        }
        .custom-table tr {
            border-bottom: 1px solid #ddd;
        }
        /* Media Query untuk layar kecil */
        @media (max-width: 768px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font */
            }
        }
        @media (max-width: 480px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font lebih kecil */
            }
        }
        </style>';

        $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
        $output .= '<table class="custom-table">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Jenis Kegiatan</th>';
        $output .= '<th style="padding:12px;">Ket</th>';
                                                        // Tambahkan kolom "Aksi" jika admin login
                                                        if (current_user_can('administrator')) {
                                                            $output .= '<th style="padding:12px;">Aksi</th>';
                                                        }
        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->jenis_kegiatan) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->ket) . '%</td>';
                                                // Jika admin login, tampilkan tombol Edit
                                                if (current_user_can('administrator')) {
                                                    $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-data-relasi-pembangunan-sungai-enam?id=' . $row->id . '">Edit</a></td>';
                                    
                                                }
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }
    if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-data-relasi-pembangunan-sungai-enam" class="tambah-button">Tambah Data Realisasi Pembangunan Kelurahan Sungai Enam</a>';
        $output .= '</div>';
    }
    return $output;
}

function edit_data_relasi_pembangunan_kelurahan_sungai_enam() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_relasi_pembangunan_kelurahan_sungai_enam'; // Nama tabel yang benar

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_relasi_pembangunan_kelurahan_sungai_enam',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/data-potensi-dan-informasi/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
            // Jika form disubmit untuk update
            if (isset($_POST['submit'])) {
                // Validasi dan sanitasi input dari form
                $jenis_kegiatan = sanitize_text_field($_POST['jenis_kegiatan']);
                $ket = intval($_POST['ket']);


                // Update data ke database
                $wpdb->update(
                    'data_relasi_pembangunan_kelurahan_sungai_enam',
                    array(
                        'jenis_kegiatan' => $jenis_kegiatan,
                        'ket' => $ket,
                    ),
                    array('id' => $id),  // Syarat untuk update berdasarkan ID
                    array('%s', '%d'),  // Format tipe data
                    array('%d')  // Format untuk ID
                );

                // Redirect setelah data berhasil diupdate
                echo '<script type="text/javascript">
                    alert("Data berhasil diupdate.");
                    window.location.href = "http://websitekecamatanbintantimur.test/data-potensi-dan-informasi/";
                </script>';
                exit;
            }

            // Tampilkan form untuk mengedit data
            return '
                <form method="post">
                    <label for="uraian">Jenis Kegiatan:</label>
                    <input type="text" name="jenis_kegiatan" value="' . esc_attr($result->jenis_kegiatan) . '"><br>
                    <label for="uraian">Keterangan:</label>
                    <input type="number" name="ket" value="' . esc_attr($result->ket) . '" inputmode="numeric" pattern="\d*"><br>
                    <input type="submit" name="submit" value="Update Data" class="update-button">
                    <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
                </form>';
        } else {
            // Jika data tidak ditemukan
            return '<p>Data tidak ditemukan.</p>';
        }
    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_data_relasi_pembangunan_kelurahan_sungai_enam', 'edit_data_relasi_pembangunan_kelurahan_sungai_enam');


function tambah_data_relasi_pembangunan_kelurahan_sungai_enam() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_relasi_pembangunan_kelurahan_sungai_enam';

    if (isset($_POST['submit'])) {
        $jenis_kegiatan = sanitize_text_field($_POST['jenis_kegiatan']);
        $ket = intval($_POST['ket']);

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_relasi_pembangunan_kelurahan_sungai_enam',
            array(
                'nomor' => $new_nomor,
                'jenis_kegiatan' => $jenis_kegiatan,
                'ket' => $ket,
            ),
            array('%d', '%s', '%d')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/data-potensi-dan-informasi/";
        </script>';
        exit;
    }

    return '
        <form method="post">
                <label for="uraian">Jenis Kegiatan:</label>
                    <input type="text" name="jenis_kegiatan" value="' . esc_attr($result->jenis_kegiatan) . '"><br>
                    <label for="uraian">Keterangan:</label>
                    <input type="number" name="ket" value="' . esc_attr($result->ket) . '" inputmode="numeric" pattern="\d*"><br>
        <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_relasi_pembangunan_kelurahan_sungai_enam', 'tambah_data_relasi_pembangunan_kelurahan_sungai_enam');


function  data_relasi_pembangunan_kelurahan_gunung_lengkuas() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_relasi_pembangunan_kelurahan_gunung_lengkuas';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
            text-align: left;
        }
        .custom-table th{
            font-size: 10px;
        }    
        .custom-table td{
            font-size: 10px;
        }  
        .custom-table th, .custom-table td {
            padding: 12px;
        }
        .custom-table thead {
            background-color: #f2f2f2;
            color: #333;
        }
        .custom-table tr {
            border-bottom: 1px solid #ddd;
        }
        /* Media Query untuk layar kecil */
        @media (max-width: 768px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font */
            }
        }
        @media (max-width: 480px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font lebih kecil */
            }
        }
        </style>';

        $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
        $output .= '<table class="custom-table">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Jenis Kegiatan</th>';
        $output .= '<th style="padding:12px;">Ket</th>';
                                                        // Tambahkan kolom "Aksi" jika admin login
                                                        if (current_user_can('administrator')) {
                                                            $output .= '<th style="padding:12px;">Aksi</th>';
                                                        }
        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->jenis_kegiatan) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->ket) . '%</td>';
                                                // Jika admin login, tampilkan tombol Edit
                                                if (current_user_can('administrator')) {
                                                    $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-data-relasi-pembangunan-gunung-lengkuas?id=' . $row->id . '">Edit</a></td>';
                                    
                                                }
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }
    if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-data-relasi-pembangunan-gunung-lengkuas" class="tambah-button">Tambah Data Realisasi Pembangunan Kelurahan Gunung Lengkuas</a>';
        $output .= '</div>';
    }
    return $output;
}

function edit_data_relasi_pembangunan_kelurahan_gunung_lengkuas() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_relasi_pembangunan_kelurahan_gunung_lengkuas'; // Nama tabel yang benar

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_relasi_pembangunan_kelurahan_gunung_lengkuas',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/data-potensi-dan-informasi/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
            // Jika form disubmit untuk update
            if (isset($_POST['submit'])) {
                // Validasi dan sanitasi input dari form
                $jenis_kegiatan = sanitize_text_field($_POST['jenis_kegiatan']);
                $ket = intval($_POST['ket']);


                // Update data ke database
                $wpdb->update(
                    'data_relasi_pembangunan_kelurahan_gunung_lengkuas',
                    array(
                        'jenis_kegiatan' => $jenis_kegiatan,
                        'ket' => $ket,
                    ),
                    array('id' => $id),  // Syarat untuk update berdasarkan ID
                    array('%s', '%d'),  // Format tipe data
                    array('%d')  // Format untuk ID
                );

                // Redirect setelah data berhasil diupdate
                echo '<script type="text/javascript">
                    alert("Data berhasil diupdate.");
                    window.location.href = "http://websitekecamatanbintantimur.test/data-potensi-dan-informasi/";
                </script>';
                exit;
            }

            // Tampilkan form untuk mengedit data
            return '
                <form method="post">
                    <label for="uraian">Jenis Kegiatan:</label>
                    <input type="text" name="jenis_kegiatan" value="' . esc_attr($result->jenis_kegiatan) . '"><br>
                    <label for="uraian">Keterangan:</label>
                    <input type="number" name="ket" value="' . esc_attr($result->ket) . '" inputmode="numeric" pattern="\d*"><br>
                    <input type="submit" name="submit" value="Update Data" class="update-button">
                    <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
                </form>';
        } else {
            // Jika data tidak ditemukan
            return '<p>Data tidak ditemukan.</p>';
        }
    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_data_relasi_pembangunan_kelurahan_gunung_lengkuas', 'edit_data_relasi_pembangunan_kelurahan_gunung_lengkuas');


function tambah_data_relasi_pembangunan_kelurahan_gunung_lengkuas() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_relasi_pembangunan_kelurahan_gunung_lengkuas';

    if (isset($_POST['submit'])) {
        $jenis_kegiatan = sanitize_text_field($_POST['jenis_kegiatan']);
        $ket = intval($_POST['ket']);

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_relasi_pembangunan_kelurahan_gunung_lengkuas',
            array(
                'nomor' => $new_nomor,
                'jenis_kegiatan' => $jenis_kegiatan,
                'ket' => $ket,
            ),
            array('%d', '%s', '%d')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/data-potensi-dan-informasi/";
        </script>';
        exit;
    }

    return '
        <form method="post">
                <label for="uraian">Jenis Kegiatan:</label>
                    <input type="text" name="jenis_kegiatan" value="' . esc_attr($result->jenis_kegiatan) . '"><br>
                    <label for="uraian">Keterangan:</label>
                    <input type="number" name="ket" value="' . esc_attr($result->ket) . '" inputmode="numeric" pattern="\d*"><br>
        <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_relasi_pembangunan_kelurahan_gunung_lengkuas', 'tambah_data_relasi_pembangunan_kelurahan_gunung_lengkuas');


function  data_relasi_pembangunan_kelurahan_sungai_lekop() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_relasi_pembangunan_kelurahan_sungai_lekop';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
            text-align: left;
        }
        .custom-table th{
            font-size: 10px;
        }    
        .custom-table td{
            font-size: 10px;
        }  
        .custom-table th, .custom-table td {
            padding: 12px;
        }
        .custom-table thead {
            background-color: #f2f2f2;
            color: #333;
        }
        .custom-table tr {
            border-bottom: 1px solid #ddd;
        }
        /* Media Query untuk layar kecil */
        @media (max-width: 768px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font */
            }
        }
        @media (max-width: 480px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font lebih kecil */
            }
        }
        </style>';

        $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
        $output .= '<table class="custom-table">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Jenis Kegiatan</th>';
        $output .= '<th style="padding:12px;">Ket</th>';
                                                        // Tambahkan kolom "Aksi" jika admin login
                                                        if (current_user_can('administrator')) {
                                                            $output .= '<th style="padding:12px;">Aksi</th>';
                                                        }
        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->jenis_kegiatan) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->ket) . '%</td>';
                                                // Jika admin login, tampilkan tombol Edit
                                                if (current_user_can('administrator')) {
                                                    $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-data-relasi-pembangunan-sungai-lekop?id=' . $row->id . '">Edit</a></td>';
                                    
                                                }
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }
    if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-data-relasi-pembangunan-sungai-lekop" class="tambah-button">Tambah Data Daerah Realisasi Pembangunan Kelurahan Sungai Lekop</a>';
        $output .= '</div>';
    }
    return $output;
}
function edit_data_relasi_pembangunan_kelurahan_sungai_lekop() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_relasi_pembangunan_kelurahan_sungai_lekop'; // Nama tabel yang benar

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_relasi_pembangunan_kelurahan_sungai_lekop',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/data-potensi-dan-informasi/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
            // Jika form disubmit untuk update
            if (isset($_POST['submit'])) {
                // Validasi dan sanitasi input dari form
                $jenis_kegiatan = sanitize_text_field($_POST['jenis_kegiatan']);
                $ket = intval($_POST['ket']);


                // Update data ke database
                $wpdb->update(
                    'data_relasi_pembangunan_kelurahan_sungai_lekop',
                    array(
                        'jenis_kegiatan' => $jenis_kegiatan,
                        'ket' => $ket,
                    ),
                    array('id' => $id),  // Syarat untuk update berdasarkan ID
                    array('%s', '%d'),  // Format tipe data
                    array('%d')  // Format untuk ID
                );

                // Redirect setelah data berhasil diupdate
                echo '<script type="text/javascript">
                    alert("Data berhasil diupdate.");
                    window.location.href = "http://websitekecamatanbintantimur.test/data-potensi-dan-informasi/";
                </script>';
                exit;
            }

            // Tampilkan form untuk mengedit data
            return '
                <form method="post">
                    <label for="uraian">Jenis Kegiatan:</label>
                    <input type="text" name="jenis_kegiatan" value="' . esc_attr($result->jenis_kegiatan) . '"><br>
                    <label for="uraian">Keterangan:</label>
                    <input type="number" name="ket" value="' . esc_attr($result->ket) . '" inputmode="numeric" pattern="\d*"><br>
                    <input type="submit" name="submit" value="Update Data" class="update-button">
                    <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
                </form>';
        } else {
            // Jika data tidak ditemukan
            return '<p>Data tidak ditemukan.</p>';
        }
    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_data_relasi_pembangunan_kelurahan_sungai_lekop', 'edit_data_relasi_pembangunan_kelurahan_sungai_lekop');


function tambah_data_relasi_pembangunan_kelurahan_sungai_lekop() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_relasi_pembangunan_kelurahan_sungai_lekop';

    if (isset($_POST['submit'])) {
        $jenis_kegiatan = sanitize_text_field($_POST['jenis_kegiatan']);
        $ket = intval($_POST['ket']);

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_relasi_pembangunan_kelurahan_sungai_lekop',
            array(
                'nomor' => $new_nomor,
                'jenis_kegiatan' => $jenis_kegiatan,
                'ket' => $ket,
            ),
            array('%d', '%s', '%d')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/data-potensi-dan-informasi/";
        </script>';
        exit;
    }

    return '
        <form method="post">
                <label for="uraian">Jenis Kegiatan:</label>
                    <input type="text" name="jenis_kegiatan" value="' . esc_attr($result->jenis_kegiatan) . '"><br>
                    <label for="uraian">Keterangan:</label>
                    <input type="number" name="ket" value="' . esc_attr($result->ket) . '" inputmode="numeric" pattern="\d*"><br>
        <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_relasi_pembangunan_kelurahan_sungai_lekop', 'tambah_data_relasi_pembangunan_kelurahan_sungai_lekop');


function  data_penyuluh_sosial_masyarakat() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_penyuluh_sosial_masyarakat';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
            text-align: left;
        }
        .custom-table th{
            font-size: 10px;
        }    
        .custom-table td{
            font-size: 10px;
        }  
        .custom-table th, .custom-table td {
            padding: 12px;
        }
        .custom-table thead {
            background-color: #f2f2f2;
            color: #333;
        }
        .custom-table tr {
            border-bottom: 1px solid #ddd;
        }
        /* Media Query untuk layar kecil */
        @media (max-width: 768px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font */
            }
        }
        @media (max-width: 480px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font lebih kecil */
            }
        }
        </style>';

        $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
        $output .= '<table class="custom-table">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Kelurahan</th>';
        $output .= '<th style="padding:12px;">Nama</th>';
        $output .= '<th style="padding:12px;">Aktif</th>';
        $output .= '<th style="padding:12px;">Ket</th>';
                                    // Tambahkan kolom "Aksi" jika admin login
                                    if (current_user_can('administrator')) {
                                        $output .= '<th style="padding:12px;">Aksi</th>';
                                    }
        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->kelurahan) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->nama) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->aktif) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->ket) . '</td>';
                                    // Jika admin login, tampilkan tombol Edit
                                    if (current_user_can('administrator')) {
                                        $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-data-penyuluh-sosial-masyarakat?id=' . $row->id . '">Edit</a></td>';
                        
                                    }
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }
    if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-data-penyuluh-sosial-masyarakat" class="tambah-button">Tambah Data Penyuluh Sosial Masyarakat</a>';
        $output .= '</div>';
    }

    return $output;
}

function edit_data_penyuluh_sosial_masyarakat() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_penyuluh_sosial_masyarakat'; // Nama tabel yang benar

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_penyuluh_sosial_masyarakat',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/data-potensi-dan-informasi/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
    // Jika form disubmit untuk update
    if (isset($_POST['submit'])) {
        // Validasi dan sanitasi input dari form
        $kelurahan = sanitize_text_field($_POST['kelurahan']);
        $nama = sanitize_text_field($_POST['nama']);
        $aktif = sanitize_text_field($_POST['aktif']) === 'aktif' ? '√' : '';  // Simpan "√" jika Aktif
        $ket = sanitize_text_field($_POST['ket']);

        // Update data ke database
        $wpdb->update(
            'data_penyuluh_sosial_masyarakat',
            array(
                'kelurahan' => $kelurahan,
                'nama' => $nama,
                'aktif' => $aktif,
                'ket' => $ket,
            ),
            array('id' => $id),  // Syarat untuk update berdasarkan ID
            array('%s', '%s', '%s', '%s'),  // Format tipe data
            array('%d')  // Format untuk ID
        );

        // Redirect setelah data berhasil diupdate
        echo '<script type="text/javascript">
            alert("Data berhasil diupdate.");
            window.location.href = "http://websitekecamatanbintantimur.test/data-potensi-dan-informasi/";
        </script>';
        exit;
    }

    // Tampilkan form untuk mengedit data
    return '
        <form method="post">
            <label for="kelurahan">Nama Kelurahan:</label>
            <input type="text" name="kelurahan" value="' . esc_attr($result->kelurahan) . '"><br>
            <label for="nama">Nama:</label>
            <input type="text" name="nama" value="' . esc_attr($result->nama) . '"><br>
            <label for="aktif">Status:</label>
            <select name="aktif">
                <option value="aktif"' . ($result->aktif === '√' ? ' selected' : '') . '>Aktif</option>
                <option value="tidak_aktif"' . ($result->aktif === '√' ? ' selected' : '') . '>Tidak Aktif</option>
            </select><br>
            <label for="ket">Keterangan:</label>
            <input type="text" name="ket" value="' . esc_attr($result->ket) . '"><br>
            <input type="submit" name="submit" value="Update Data" class="update-button">
            <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
        </form>';
} else {
    // Jika data tidak ditemukan
    return '<p>Data tidak ditemukan.</p>';
}

    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_data_penyuluh_sosial_masyarakat', 'edit_data_penyuluh_sosial_masyarakat');


function tambah_data_penyuluh_sosial_masyarakat() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_penyuluh_sosial_masyarakat';

    if (isset($_POST['submit'])) {
        $kelurahan = sanitize_text_field($_POST['kelurahan']);
        $nama = sanitize_text_field($_POST['nama']);
        $aktif = sanitize_text_field($_POST['aktif']) === 'aktif' ? '√' : '';  // Simpan "√" jika Aktif
        $ket = sanitize_text_field($_POST['ket']);

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_penyuluh_sosial_masyarakat',
            array(
                'nomor' => $new_nomor,
                'kelurahan' => $kelurahan,
                'nama' => $nama,
                'aktif' => $aktif,
                'ket' => $ket,
            ),
            array('%d', '%s', '%s', '%s', '%s')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/data-potensi-dan-informasi/";
        </script>';
        exit;
    }

    return '
        <form method="post">
        <label for="kelurahan">Nama Kelurahan:</label>
            <input type="text" name="kelurahan" value="' . esc_attr($result->kelurahan) . '"><br>
            <label for="nama">Nama:</label>
            <input type="text" name="nama" value="' . esc_attr($result->nama) . '"><br>
            <label for="aktif">Status:</label>
            <select name="aktif">
                <option value="aktif"' . ($result->aktif === '√' ? ' selected' : '') . '>Aktif</option>
                <option value="tidak_aktif"' . ($result->aktif === '√' ? ' selected' : '') . '>Tidak Aktif</option>
            </select><br>
            <label for="ket">Keterangan:</label>
            <input type="text" name="ket" value="' . esc_attr($result->ket) . '"><br>
        <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_penyuluh_sosial_masyarakat', 'tambah_data_penyuluh_sosial_masyarakat');


function  data_pendamping_lansia() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_pendamping_lansia';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
            text-align: left;
        }
        .custom-table th{
            font-size: 10px;
        }    
        .custom-table td{
            font-size: 10px;
        }  
        .custom-table th, .custom-table td {
            padding: 12px;
        }
        .custom-table thead {
            background-color: #f2f2f2;
            color: #333;
        }
        .custom-table tr {
            border-bottom: 1px solid #ddd;
        }
        /* Media Query untuk layar kecil */
        @media (max-width: 768px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font */
            }
        }
        @media (max-width: 480px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font lebih kecil */
            }
        }
        </style>';

        $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
        $output .= '<table class="custom-table">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Kelurahan</th>';
        $output .= '<th style="padding:12px;">Nama</th>';
        $output .= '<th style="padding:12px;">Aktif</th>';
        $output .= '<th style="padding:12px;">Ket</th>';
                                    // Tambahkan kolom "Aksi" jika admin login
                            if (current_user_can('administrator')) {
                                $output .= '<th style="padding:12px;">Aksi</th>';
                            }
        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->kelurahan) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->nama) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->aktif) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->ket) . '</td>';
                                    // Jika admin login, tampilkan tombol Edit
                                    if (current_user_can('administrator')) {
                                        $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-data-pendamping-lansia?id=' . $row->id . '">Edit</a></td>';
                        
                                    }
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }
    if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-data-pendamping-lansia" class="tambah-button">Tambah Data Pendamping Lansia</a>';
        $output .= '</div>';
    }

    return $output;
}

function edit_data_pendamping_lansia() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_pendamping_lansia'; // Nama tabel yang benar

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_pendamping_lansia',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/data-potensi-dan-informasi/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
    // Jika form disubmit untuk update
    if (isset($_POST['submit'])) {
        // Validasi dan sanitasi input dari form
        $kelurahan = sanitize_text_field($_POST['kelurahan']);
        $nama = sanitize_text_field($_POST['nama']);
        $aktif = sanitize_text_field($_POST['aktif']) === 'aktif' ? '√' : '';  // Simpan "√" jika Aktif
        $ket = sanitize_text_field($_POST['ket']);

        // Update data ke database
        $wpdb->update(
            'data_pendamping_lansia',
            array(
                'kelurahan' => $kelurahan,
                'nama' => $nama,
                'aktif' => $aktif,
                'ket' => $ket,
            ),
            array('id' => $id),  // Syarat untuk update berdasarkan ID
            array('%s', '%s', '%s', '%s'),  // Format tipe data
            array('%d')  // Format untuk ID
        );

        // Redirect setelah data berhasil diupdate
        echo '<script type="text/javascript">
            alert("Data berhasil diupdate.");
            window.location.href = "http://websitekecamatanbintantimur.test/data-potensi-dan-informasi/";
        </script>';
        exit;
    }

    // Tampilkan form untuk mengedit data
    return '
        <form method="post">
            <label for="kelurahan">Nama Kelurahan:</label>
            <input type="text" name="kelurahan" value="' . esc_attr($result->kelurahan) . '"><br>
            <label for="nama">Nama:</label>
            <input type="text" name="nama" value="' . esc_attr($result->nama) . '"><br>
            <label for="aktif">Status:</label>
            <select name="aktif">
                <option value="aktif"' . ($result->aktif === '√' ? ' selected' : '') . '>Aktif</option>
                <option value="tidak_aktif"' . ($result->aktif === '√' ? ' selected' : '') . '>Tidak Aktif</option>
            </select><br>
            <label for="ket">Keterangan:</label>
            <input type="text" name="ket" value="' . esc_attr($result->ket) . '"><br>
            <input type="submit" name="submit" value="Update Data" class="update-button">
            <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
        </form>';
} else {
    // Jika data tidak ditemukan
    return '<p>Data tidak ditemukan.</p>';
}

    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_data_pendamping_lansia', 'edit_data_pendamping_lansia');


function tambah_data_pendamping_lansia() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_pendamping_lansia';

    if (isset($_POST['submit'])) {
        $kelurahan = sanitize_text_field($_POST['kelurahan']);
        $nama = sanitize_text_field($_POST['nama']);
        $aktif = sanitize_text_field($_POST['aktif']) === 'aktif' ? '√' : '';  // Simpan "√" jika Aktif
        $ket = sanitize_text_field($_POST['ket']);

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_pendamping_lansia',
            array(
                'nomor' => $new_nomor,
                'kelurahan' => $kelurahan,
                'nama' => $nama,
                'aktif' => $aktif,
                'ket' => $ket,
            ),
            array('%d', '%s', '%s', '%s', '%s')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/data-potensi-dan-informasi/";
        </script>';
        exit;
    }

    return '
        <form method="post">
        <label for="kelurahan">Nama Kelurahan:</label>
            <input type="text" name="kelurahan" value="' . esc_attr($result->kelurahan) . '"><br>
            <label for="nama">Nama:</label>
            <input type="text" name="nama" value="' . esc_attr($result->nama) . '"><br>
            <label for="aktif">Status:</label>
            <select name="aktif">
                <option value="aktif"' . ($result->aktif === '√' ? ' selected' : '') . '>Aktif</option>
                <option value="tidak_aktif"' . ($result->aktif === '√' ? ' selected' : '') . '>Tidak Aktif</option>
            </select><br>
            <label for="ket">Keterangan:</label>
            <input type="text" name="ket" value="' . esc_attr($result->ket) . '"><br>
        <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_pendamping_lansia', 'tambah_data_pendamping_lansia');


function  data_tpk_kelurahan_kijang_kota() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_tpk_kelurahan_kijang_kota';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
            text-align: left;
        }
        .custom-table th{
            font-size: 10px;
        }    
        .custom-table td{
            font-size: 10px;
        }  
        .custom-table th, .custom-table td {
            padding: 12px;
        }
        .custom-table thead {
            background-color: #f2f2f2;
            color: #333;
        }
        .custom-table tr {
            border-bottom: 1px solid #ddd;
        }
        /* Media Query untuk layar kecil */
        @media (max-width: 768px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font */
            }
        }
        @media (max-width: 480px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font lebih kecil */
            }
        }
        </style>';

        $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
        $output .= '<table class="custom-table">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Insitusi</th>';
        $output .= '<th style="padding:12px;">Nama</th>';
        $output .= '<th style="padding:12px;">No. Telp</th>';
                                    // Tambahkan kolom "Aksi" jika admin login
                                    if (current_user_can('administrator')) {
                                        $output .= '<th style="padding:12px;">Aksi</th>';
                                    }
        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            $output .= '<td style="padding:12px;">' . format_text($row->insitusi) . '</td>';
            $output .= '<td style="padding:12px;">' . format_text($row->nama) . '</td>';
            $output .= '<td style="padding:12px;">' . format_text($row->no_telp) . '</td>';
                                    // Jika admin login, tampilkan tombol Edit
                                    if (current_user_can('administrator')) {
                                        $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-data-tim-pendamping-keluarga-tpk-kelurahan-kijang-Kota?id=' . $row->id . '">Edit</a></td>';
                        
                                    }
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }
    if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-data-tim-pendamping-keluarga-tpk-kelurahan-kijang-Kota" class="tambah-button">Tambah Data Tim Pendamping Keluarga (TPK) Kelurahan Kijang Kota</a>';
        $output .= '</div>';
    }

    return $output;
}

function edit_data_tpk_kelurahan_kijang_kota() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_tpk_kelurahan_kijang_kota'; // Nama tabel yang benar

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_tpk_kelurahan_kijang_kota',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/data-potensi-dan-informasi/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
            // Jika form disubmit untuk update
            if (isset($_POST['submit'])) {
                // Validasi dan sanitasi input dari form
                $insitusi = sanitize_text_field($_POST['insitusi']);
                $nama = sanitize_text_field($_POST['nama']);
                $no_telp = sanitize_text_field($_POST['no_telp']);


                // Update data ke database
                $wpdb->update(
                    'data_tpk_kelurahan_kijang_kota',
                    array(
                        'insitusi' => $insitusi,
                        'nama' => $nama,
                        'no_telp' => $no_telp,
                    ),
                    array('id' => $id),  // Syarat untuk update berdasarkan ID
                    array('%s', '%s', '%s'),  // Format tipe data
                    array('%d')  // Format untuk ID
                );

                // Redirect setelah data berhasil diupdate
                echo '<script type="text/javascript">
                    alert("Data berhasil diupdate.");
                    window.location.href = "http://websitekecamatanbintantimur.test/data-potensi-dan-informasi/";
                </script>';
                exit;
            }

            // Tampilkan form untuk mengedit data
            return '
                <form method="post">
                    <label for="uraian">Nama Insitusi:</label>
                    <input type="text" name="insitusi" value="' . esc_attr($result->insitusi) . '"><br>
                    <label for="uraian">Nama:</label>
                    <input type="text" name="nama" value="' . esc_attr($result->nama) . '"><br>
                    <label for="uraian">No.Teleponp:</label>
                    <input type="text" name="no_telp" value="' . esc_attr($result->no_telp) . '"><br>
                    <input type="submit" name="submit" value="Update Data" class="update-button">
                    <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
                </form>';
        } else {
            // Jika data tidak ditemukan
            return '<p>Data tidak ditemukan.</p>';
        }
    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_data_tpk_kelurahan_kijang_kota', 'edit_data_tpk_kelurahan_kijang_kota');


function tambah_data_tpk_kelurahan_kijang_kota() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_tpk_kelurahan_kijang_kota';

    if (isset($_POST['submit'])) {
        $insitusi = sanitize_text_field($_POST['insitusi']);
        $nama = sanitize_text_field($_POST['nama']);
        $no_telp = sanitize_text_field($_POST['no_telp']);

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_tpk_kelurahan_kijang_kota',
            array(
                'nomor' => $new_nomor,
                'insitusi' => $insitusi,
                'nama' => $nama,
                'no_telp' => $no_telp,
            ),
            array('%d', '%s', '%s', '%s')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/data-potensi-dan-informasi/";
        </script>';
        exit;
    }

    return '
        <form method="post">
        <label for="uraian">Nama Insitusi:</label>
                    <input type="text" name="insitusi" value="' . esc_attr($result->insitusi) . '"><br>
                    <label for="uraian">Nama:</label>
                    <input type="text" name="nama" value="' . esc_attr($result->nama) . '"><br>
                    <label for="uraian">No.Teleponp:</label>
                    <input type="text" name="no_telp" value="' . esc_attr($result->no_telp) . '"><br>
        <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_tpk_kelurahan_kijang_kota', 'tambah_data_tpk_kelurahan_kijang_kota');


function  data_tpk_kelurahan_sungai_enam() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_tpk_kelurahan_sungai_enam';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
            text-align: left;
        }
        .custom-table th{
            font-size: 10px;
        }    
        .custom-table td{
            font-size: 10px;
        }  
        .custom-table th, .custom-table td {
            padding: 12px;
        }
        .custom-table thead {
            background-color: #f2f2f2;
            color: #333;
        }
        .custom-table tr {
            border-bottom: 1px solid #ddd;
        }
        /* Media Query untuk layar kecil */
        @media (max-width: 768px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font */
            }
        }
        @media (max-width: 480px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font lebih kecil */
            }
        }
        </style>';

        $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
        $output .= '<table class="custom-table">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Insitusi</th>';
        $output .= '<th style="padding:12px;">Nama</th>';
        $output .= '<th style="padding:12px;">No. Telp</th>';
                                    // Tambahkan kolom "Aksi" jika admin login
                                    if (current_user_can('administrator')) {
                                        $output .= '<th style="padding:12px;">Aksi</th>';
                                    }
        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            $output .= '<td style="padding:12px;">' . format_text($row->insitusi) . '</td>';
            $output .= '<td style="padding:12px;">' . format_text($row->nama) . '</td>';
            $output .= '<td style="padding:12px;">' . format_text($row->no_telp) . '</td>';
                                                // Jika admin login, tampilkan tombol Edit
                                                if (current_user_can('administrator')) {
                                                    $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-data-tim-pendamping-keluarga-tpk-kelurahan-sungai-enam?id=' . $row->id . '">Edit</a></td>';
                                    
                                                }
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }
    if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-data-tim-pendamping-keluarga-tpk-kelurahan-sungai-enam" class="tambah-button">Tambah Data Tim Pendamping Keluarga (TPK) Kelurahan Sungai Enam</a>';
        $output .= '</div>';
    }
    return $output;
}

function edit_data_tpk_kelurahan_sungai_enam() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_tpk_kelurahan_sungai_enam'; // Nama tabel yang benar

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_tpk_kelurahan_sungai_enam',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/data-potensi-dan-informasi/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
            // Jika form disubmit untuk update
            if (isset($_POST['submit'])) {
                // Validasi dan sanitasi input dari form
                $insitusi = sanitize_text_field($_POST['insitusi']);
                $nama = sanitize_text_field($_POST['nama']);
                $no_telp = sanitize_text_field($_POST['no_telp']);


                // Update data ke database
                $wpdb->update(
                    'data_tpk_kelurahan_sungai_enam',
                    array(
                        'insitusi' => $insitusi,
                        'nama' => $nama,
                        'no_telp' => $no_telp,
                    ),
                    array('id' => $id),  // Syarat untuk update berdasarkan ID
                    array('%s', '%s', '%s'),  // Format tipe data
                    array('%d')  // Format untuk ID
                );

                // Redirect setelah data berhasil diupdate
                echo '<script type="text/javascript">
                    alert("Data berhasil diupdate.");
                    window.location.href = "http://websitekecamatanbintantimur.test/data-potensi-dan-informasi/";
                </script>';
                exit;
            }

            // Tampilkan form untuk mengedit data
            return '
                <form method="post">
                    <label for="uraian">Nama Insitusi:</label>
                    <input type="text" name="insitusi" value="' . esc_attr($result->insitusi) . '"><br>
                    <label for="uraian">Nama:</label>
                    <input type="text" name="nama" value="' . esc_attr($result->nama) . '"><br>
                    <label for="uraian">No.Teleponp:</label>
                    <input type="text" name="no_telp" value="' . esc_attr($result->no_telp) . '"><br>
                    <input type="submit" name="submit" value="Update Data" class="update-button">
                    <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
                </form>';
        } else {
            // Jika data tidak ditemukan
            return '<p>Data tidak ditemukan.</p>';
        }
    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_data_tpk_kelurahan_sungai_enam', 'edit_data_tpk_kelurahan_sungai_enam');


function tambah_data_tpk_kelurahan_sungai_enam() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_tpk_kelurahan_sungai_enam';

    if (isset($_POST['submit'])) {
        $insitusi = sanitize_text_field($_POST['insitusi']);
        $nama = sanitize_text_field($_POST['nama']);
        $no_telp = sanitize_text_field($_POST['no_telp']);

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_tpk_kelurahan_sungai_enam',
            array(
                'nomor' => $new_nomor,
                'insitusi' => $insitusi,
                'nama' => $nama,
                'no_telp' => $no_telp,
            ),
            array('%d', '%s', '%s', '%s')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/data-potensi-dan-informasi/";
        </script>';
        exit;
    }

    return '
        <form method="post">
        <label for="uraian">Nama Insitusi:</label>
                    <input type="text" name="insitusi" value="' . esc_attr($result->insitusi) . '"><br>
                    <label for="uraian">Nama:</label>
                    <input type="text" name="nama" value="' . esc_attr($result->nama) . '"><br>
                    <label for="uraian">No.Teleponp:</label>
                    <input type="text" name="no_telp" value="' . esc_attr($result->no_telp) . '"><br>
        <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_tpk_kelurahan_sungai_enam', 'tambah_data_tpk_kelurahan_sungai_enam');


function  data_tpk_kelurahan_gunung_lengkuas() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_tpk_kelurahan_gunung_lengkuas';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
            text-align: left;
        }
        .custom-table th{
            font-size: 10px;
        }    
        .custom-table td{
            font-size: 10px;
        }  
        .custom-table th, .custom-table td {
            padding: 12px;
        }
        .custom-table thead {
            background-color: #f2f2f2;
            color: #333;
        }
        .custom-table tr {
            border-bottom: 1px solid #ddd;
        }
        /* Media Query untuk layar kecil */
        @media (max-width: 768px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font */
            }
        }
        @media (max-width: 480px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font lebih kecil */
            }
        }
        </style>';

        $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
        $output .= '<table class="custom-table">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Insitusi</th>';
        $output .= '<th style="padding:12px;">Nama</th>';
        $output .= '<th style="padding:12px;">No. Telp</th>';
                                    // Tambahkan kolom "Aksi" jika admin login
                                    if (current_user_can('administrator')) {
                                        $output .= '<th style="padding:12px;">Aksi</th>';
                                    }
        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            $output .= '<td style="padding:12px;">' . format_text($row->insitusi) . '</td>';
            $output .= '<td style="padding:12px;">' . format_text($row->nama) . '</td>';
            $output .= '<td style="padding:12px;">' . format_text($row->no_telp) . '</td>';
            
                                    // Jika admin login, tampilkan tombol Edit
                                    if (current_user_can('administrator')) {
                                        $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-data-tim-pendamping-keluarga-tpk-kelurahan-gunung-lengkuas?id=' . $row->id . '">Edit</a></td>';
                        
                                    }
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }
    if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-data-tim-pendamping-keluarga-tpk-kelurahan-gunung-lengkuas" class="tambah-button">Tambah Data Tim Pendamping Keluarga (TPK) Kelurahan Gunung Lengkuas</a>';
        $output .= '</div>';
    }
    return $output;
}

function edit_data_tpk_kelurahan_gunung_lengkuas() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_tpk_kelurahan_gunung_lengkuas'; // Nama tabel yang benar

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_tpk_kelurahan_gunung_lengkuas',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/data-potensi-dan-informasi/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
            // Jika form disubmit untuk update
            if (isset($_POST['submit'])) {
                // Validasi dan sanitasi input dari form
                $insitusi = sanitize_text_field($_POST['insitusi']);
                $nama = sanitize_text_field($_POST['nama']);
                $no_telp = sanitize_text_field($_POST['no_telp']);


                // Update data ke database
                $wpdb->update(
                    'data_tpk_kelurahan_gunung_lengkuas',
                    array(
                        'insitusi' => $insitusi,
                        'nama' => $nama,
                        'no_telp' => $no_telp,
                    ),
                    array('id' => $id),  // Syarat untuk update berdasarkan ID
                    array('%s', '%s', '%s'),  // Format tipe data
                    array('%d')  // Format untuk ID
                );

                // Redirect setelah data berhasil diupdate
                echo '<script type="text/javascript">
                    alert("Data berhasil diupdate.");
                    window.location.href = "http://websitekecamatanbintantimur.test/data-potensi-dan-informasi/";
                </script>';
                exit;
            }

            // Tampilkan form untuk mengedit data
            return '
                <form method="post">
                    <label for="uraian">Nama Insitusi:</label>
                    <input type="text" name="insitusi" value="' . esc_attr($result->insitusi) . '"><br>
                    <label for="uraian">Nama:</label>
                    <input type="text" name="nama" value="' . esc_attr($result->nama) . '"><br>
                    <label for="uraian">No.Teleponp:</label>
                    <input type="text" name="no_telp" value="' . esc_attr($result->no_telp) . '"><br>
                    <input type="submit" name="submit" value="Update Data" class="update-button">
                    <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
                </form>';
        } else {
            // Jika data tidak ditemukan
            return '<p>Data tidak ditemukan.</p>';
        }
    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_data_tpk_kelurahan_gunung_lengkuas', 'edit_data_tpk_kelurahan_gunung_lengkuas');


function tambah_data_tpk_kelurahan_gunung_lengkuas() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_tpk_kelurahan_gunung_lengkuas';

    if (isset($_POST['submit'])) {
        $insitusi = sanitize_text_field($_POST['insitusi']);
        $nama = sanitize_text_field($_POST['nama']);
        $no_telp = sanitize_text_field($_POST['no_telp']);

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_tpk_kelurahan_gunung_lengkuas',
            array(
                'nomor' => $new_nomor,
                'insitusi' => $insitusi,
                'nama' => $nama,
                'no_telp' => $no_telp,
            ),
            array('%d', '%s', '%s', '%s')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/data-potensi-dan-informasi/";
        </script>';
        exit;
    }

    return '
        <form method="post">
        <label for="uraian">Nama Insitusi:</label>
                    <input type="text" name="insitusi" value="' . esc_attr($result->insitusi) . '"><br>
                    <label for="uraian">Nama:</label>
                    <input type="text" name="nama" value="' . esc_attr($result->nama) . '"><br>
                    <label for="uraian">No.Teleponp:</label>
                    <input type="text" name="no_telp" value="' . esc_attr($result->no_telp) . '"><br>
        <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_tpk_kelurahan_gunung_lengkuas', 'tambah_data_tpk_kelurahan_gunung_lengkuas');


function  data_tpk_kelurahan_sungai_lekop() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_tpk_kelurahan_sungai_lekop';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
            text-align: left;
        }
        .custom-table th{
            font-size: 10px;
        }    
        .custom-table td{
            font-size: 10px;
        }  
        .custom-table th, .custom-table td {
            padding: 12px;
        }
        .custom-table thead {
            background-color: #f2f2f2;
            color: #333;
        }
        .custom-table tr {
            border-bottom: 1px solid #ddd;
        }
        /* Media Query untuk layar kecil */
        @media (max-width: 768px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font */
            }
        }
        @media (max-width: 480px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font lebih kecil */
            }
        }
        </style>';

        $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
        $output .= '<table class="custom-table">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Insitusi</th>';
        $output .= '<th style="padding:12px;">Nama</th>';
        $output .= '<th style="padding:12px;">No. Telp</th>';
                                    // Tambahkan kolom "Aksi" jika admin login
                                    if (current_user_can('administrator')) {
                                        $output .= '<th style="padding:12px;">Aksi</th>';
                                    }
        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            $output .= '<td style="padding:12px;">' . format_text($row->insitusi) . '</td>';
            $output .= '<td style="padding:12px;">' . format_text($row->nama) . '</td>';
            $output .= '<td style="padding:12px;">' . format_text($row->no_telp) . '</td>';
            
                                    // Jika admin login, tampilkan tombol Edit
                                    if (current_user_can('administrator')) {
                                        $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-data-tim-pendamping-keluarga-tpk-kelurahan-sungai-lekop?id=' . $row->id . '">Edit</a></td>';
                        
                                    }
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }
    if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-data-tim-pendamping-keluarga-tpk-kelurahan-sungai-lekop" class="tambah-button">Tambah Data Tim Pendamping Keluarga (TPK) Kelurahan Sungai Lekop</a>';
        $output .= '</div>';
    }
    return $output;
}

function edit_data_tpk_kelurahan_sungai_lekop() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_tpk_kelurahan_sungai_lekop'; // Nama tabel yang benar

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_tpk_kelurahan_sungai_lekop',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/data-potensi-dan-informasi/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
            // Jika form disubmit untuk update
            if (isset($_POST['submit'])) {
                // Validasi dan sanitasi input dari form
                $insitusi = sanitize_text_field($_POST['insitusi']);
                $nama = sanitize_text_field($_POST['nama']);
                $no_telp = sanitize_text_field($_POST['no_telp']);


                // Update data ke database
                $wpdb->update(
                    'data_tpk_kelurahan_sungai_lekop',
                    array(
                        'insitusi' => $insitusi,
                        'nama' => $nama,
                        'no_telp' => $no_telp,
                    ),
                    array('id' => $id),  // Syarat untuk update berdasarkan ID
                    array('%s', '%s', '%s'),  // Format tipe data
                    array('%d')  // Format untuk ID
                );

                // Redirect setelah data berhasil diupdate
                echo '<script type="text/javascript">
                    alert("Data berhasil diupdate.");
                    window.location.href = "http://websitekecamatanbintantimur.test/data-potensi-dan-informasi/";
                </script>';
                exit;
            }

            // Tampilkan form untuk mengedit data
            return '
                <form method="post">
                    <label for="uraian">Nama Insitusi:</label>
                    <input type="text" name="insitusi" value="' . esc_attr($result->insitusi) . '"><br>
                    <label for="uraian">Nama:</label>
                    <input type="text" name="nama" value="' . esc_attr($result->nama) . '"><br>
                    <label for="uraian">No.Teleponp:</label>
                    <input type="text" name="no_telp" value="' . esc_attr($result->no_telp) . '"><br>
                    <input type="submit" name="submit" value="Update Data" class="update-button">
                    <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
                </form>';
        } else {
            // Jika data tidak ditemukan
            return '<p>Data tidak ditemukan.</p>';
        }
    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_data_tpk_kelurahan_sungai_lekop', 'edit_data_tpk_kelurahan_sungai_lekop');


function tambah_data_tpk_kelurahan_sungai_lekop() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_tpk_kelurahan_sungai_lekop';

    if (isset($_POST['submit'])) {
        $insitusi = sanitize_text_field($_POST['insitusi']);
        $nama = sanitize_text_field($_POST['nama']);
        $no_telp = sanitize_text_field($_POST['no_telp']);

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_tpk_kelurahan_sungai_lekop',
            array(
                'nomor' => $new_nomor,
                'insitusi' => $insitusi,
                'nama' => $nama,
                'no_telp' => $no_telp,
            ),
            array('%d', '%s', '%s', '%s')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/data-potensi-dan-informasi/";
        </script>';
        exit;
    }

    return '
        <form method="post">
        <label for="uraian">Nama Insitusi:</label>
                    <input type="text" name="insitusi" value="' . esc_attr($result->insitusi) . '"><br>
                    <label for="uraian">Nama:</label>
                    <input type="text" name="nama" value="' . esc_attr($result->nama) . '"><br>
                    <label for="uraian">No.Teleponp:</label>
                    <input type="text" name="no_telp" value="' . esc_attr($result->no_telp) . '"><br>
        <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_tpk_kelurahan_sungai_lekop', 'tambah_data_tpk_kelurahan_sungai_lekop');


function  data_ppkbd() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_ppkbd';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
            text-align: left;
        }
        .custom-table th{
            font-size: 10px;
        }    
        .custom-table td{
            font-size: 10px;
        }  
        .custom-table th, .custom-table td {
            padding: 12px;
        }
        .custom-table thead {
            background-color: #f2f2f2;
            color: #333;
        }
        .custom-table tr {
            border-bottom: 1px solid #ddd;
        }
        /* Media Query untuk layar kecil */
        @media (max-width: 768px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font */
            }
        }
        @media (max-width: 480px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font lebih kecil */
            }
        }
        </style>';

        $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
        $output .= '<table class="custom-table">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Kelurahan</th>';
        $output .= '<th style="padding:12px;">PPKBD</th>';
        $output .= '<th style="padding:12px;">SUB-PPKBD</th>';
        $output .= '<th style="padding:12px;">Aktif</th>';
                                    // Tambahkan kolom "Aksi" jika admin login
                            if (current_user_can('administrator')) {
                                $output .= '<th style="padding:12px;">Aksi</th>';
                            }
        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->kelurahan) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->ppkbd) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->sub_ppkbd) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->aktif) . '</td>';
                                    // Jika admin login, tampilkan tombol Edit
                        if (current_user_can('administrator')) {
                            $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-data-pembantu-pembina-keluarga-berencana-desa-ppkbd-sub-ppkbd?id=' . $row->id . '">Edit</a></td>';
            
                        }
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }
    if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-data-pembantu-pembina-keluarga-berencana-desa-ppkbd-sub-ppkbd" class="tambah-button">Tambah Data Pembantu Pembina Keluarga Berencana Desa (PPKBD / SUB-PPKBD)</a>';
        $output .= '</div>';
    }
    return $output;
}

function edit_data_ppkbd() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $ppkbd_database = 'websitekecamatanbintantimur';
    $table_name = $ppkbd_database . '.data_ppkbd'; // ppkbd tabel yang benar

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_ppkbd',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/data-potensi-dan-informasi/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
            // Jika form disubmit untuk update
            if (isset($_POST['submit'])) {
                // Validasi dan sanitasi input dari form
                $kelurahan = sanitize_text_field($_POST['kelurahan']);
                $ppkbd = sanitize_text_field($_POST['ppkbd']);
                $sub_ppkbd = sanitize_text_field($_POST['sub_ppkbd']);
                $aktif = sanitize_text_field($_POST['aktif']) === 'aktif' ? '√' : '';  // Simpan "√" jika Aktif


                // Update data ke database
                $wpdb->update(
                    'data_ppkbd',
                    array(
                        'kelurahan' => $kelurahan,
                        'ppkbd' => $ppkbd,
                        'sub_ppkbd' => $sub_ppkbd,
                        'aktif' => $aktif,
                    ),
                    array('id' => $id),  // Syarat untuk update berdasarkan ID
                    array('%s', '%s', '%s', '%s'),  // Format tipe data
                    array('%d')  // Format untuk ID
                );

                // Redirect setelah data berhasil diupdate
                echo '<script type="text/javascript">
                    alert("Data berhasil diupdate.");
                    window.location.href = "http://websitekecamatanbintantimur.test/data-potensi-dan-informasi/";
                </script>';
                exit;
            }

            // Tampilkan form untuk mengedit data
            return '
                <form method="post">
                    <label for="uraian">Kelurahan:</label>
                    <input type="text" name="kelurahan" value="' . esc_attr($result->kelurahan) . '"><br>
                    <label for="uraian">Nama PPKBD:</label>
                    <input type="text" name="ppkbd" value="' . esc_attr($result->ppkbd) . '"><br>
                    <label for="uraian">Nama Sub-PPKBD:</label>
                    <input type="text" name="sub_ppkbd" value="' . esc_attr($result->sub_ppkbd) . '"><br>
                    <label for="uraian">Status:</label>
                    <select name="aktif">
                        <option value="aktif"' . ($result->aktif === '√' ? ' selected' : '') . '>Aktif</option>
                        <option value="tidak_aktif"' . ($result->aktif === '√' ? ' selected' : '') . '>Tidak Aktif</option>
                    </select><br>
                    <input type="submit" name="submit" value="Update Data" class="update-button">
                    <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
                </form>';
        } else {
            // Jika data tidak ditemukan
            return '<p>Data tidak ditemukan.</p>';
        }
    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_data_ppkbd', 'edit_data_ppkbd');


function tambah_data_ppkbd() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $ppkbd_database = 'websitekecamatanbintantimur';
    $table_name = $ppkbd_database . '.data_ppkbd';

    if (isset($_POST['submit'])) {
        $kelurahan = sanitize_text_field($_POST['kelurahan']);
        $ppkbd = sanitize_text_field($_POST['ppkbd']);
        $sub_ppkbd = sanitize_text_field($_POST['sub_ppkbd']);
        $aktif = sanitize_text_field($_POST['aktif']) === 'aktif' ? '√' : '';  // Simpan "√" jika Aktif

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_ppkbd',
            array(
                'nomor' => $new_nomor,
                'kelurahan' => $kelurahan,
                'ppkbd' => $ppkbd,
                'sub_ppkbd' => $sub_ppkbd,
                'aktif' => $aktif,
            ),
            array('%d', '%s', '%s', '%s', '%s')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/data-potensi-dan-informasi/";
        </script>';
        exit;
    }

    return '
        <form method="post">
        <label for="uraian">Kelurahan:</label>
                    <input type="text" name="kelurahan" value="' . esc_attr($result->kelurahan) . '"><br>
                    <label for="uraian">Nama PPKBD:</label>
                    <input type="text" name="ppkbd" value="' . esc_attr($result->ppkbd) . '"><br>
                    <label for="uraian">Nama Sub-PPKBD:</label>
                    <input type="text" name="sub_ppkbd" value="' . esc_attr($result->sub_ppkbd) . '"><br>
                    <label for="uraian">Status:</label>
                    <select name="aktif">
                        <option value="aktif"' . ($result->aktif === '√' ? ' selected' : '') . '>Aktif</option>
                        <option value="tidak_aktif"' . ($result->aktif === '√' ? ' selected' : '') . '>Tidak Aktif</option>
                    </select><br>
        <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_ppkbd', 'tambah_data_ppkbd');


function  data_posyandu_menurut_strata() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_posyandu_menurut_strata';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
            text-align: left;
        }
        .custom-table th{
            font-size: 10px;
        }    
        .custom-table td{
            font-size: 10px;
        }  
        .custom-table th, .custom-table td {
            padding: 12px;
        }
        .custom-table thead {
            background-color: #f2f2f2;
            color: #333;
        }
        .custom-table tr {
            border-bottom: 1px solid #ddd;
        }
        /* Media Query untuk layar kecil */
        @media (max-width: 768px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font */
            }
        }
        @media (max-width: 480px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font lebih kecil */
            }
        }
        </style>';

        $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
        $output .= '<table class="custom-table">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Kelurahan</th>';
        $output .= '<th style="padding:12px;">Posyandu Pratama</th>';
        $output .= '<th style="padding:12px;">Posyandu Madya</th>';
        $output .= '<th style="padding:12px;">Posyandu Purnama</th>';
        $output .= '<th style="padding:12px;">Posyandu Mandiri</th>';
        $output .= '<th style="padding:12px;">Jumlah</th>';
                                    // Tambahkan kolom "Aksi" jika admin login
                                    if (current_user_can('administrator')) {
                                        $output .= '<th style="padding:12px;">Aksi</th>';
                                    }
        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->kelurahan) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->posyandu_pratama) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->posyandu_madya) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->posyandu_purnama) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->posyandu_mandiri) . '</td>';
            $jumlah = $row->posyandu_pratama + $row->posyandu_madya + $row->posyandu_purnama + $row->posyandu_mandiri;
            $output .= '<td style="padding:12px;">' . esc_html($jumlah) . '</td>';
                                    // Jika admin login, tampilkan tombol Edit
                                    if (current_user_can('administrator')) {
                                        $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-data-jumlah-posyandu-menurut-strata?id=' . $row->id . '">Edit</a></td>';
                        
                                    }
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }

    if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-data-jumlah-posyandu-menurut-strata" class="tambah-button">Tambah Data Jumlah Posyandu Menurut Strata</a>';
        $output .= '</div>';
    }

    return $output;
}

function edit_data_posyandu_menurut_strata() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $ppkbd_database = 'websitekecamatanbintantimur';
    $table_name = $ppkbd_database . '.data_posyandu_menurut_strata'; // ppkbd tabel yang benar

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_posyandu_menurut_strata',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/data-potensi-dan-informasi/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
            // Jika form disubmit untuk update
            if (isset($_POST['submit'])) {
                // Validasi dan sanitasi input dari form
                $kelurahan = sanitize_text_field($_POST['kelurahan']);
                $posyandu_pratama = intval($_POST['posyandu_pratama']);
                $posyandu_madya = intval($_POST['posyandu_madya']);
                $posyandu_purnama = intval($_POST['posyandu_purnama']);
                $posyandu_mandiri = intval($_POST['posyandu_mandiri']);

                // Update data ke database
                $wpdb->update(
                    'data_posyandu_menurut_strata',
                    array(
                        'kelurahan' => $kelurahan,
                        'posyandu_pratama' => $posyandu_pratama,
                        'posyandu_madya' => $posyandu_madya,
                        'posyandu_purnama' => $posyandu_purnama,
                        'posyandu_mandiri' => $posyandu_mandiri,
                    ),
                    array('id' => $id),  // Syarat untuk update berdasarkan ID
                    array('%s', '%d', '%d', '%d', '%d'),  // Format tipe data
                    array('%d')  // Format untuk ID
                );

                // Redirect setelah data berhasil diupdate
                echo '<script type="text/javascript">
                    alert("Data berhasil diupdate.");
                    window.location.href = "http://websitekecamatanbintantimur.test/data-potensi-dan-informasi/";
                </script>';
                exit;
            }

            // Tampilkan form untuk mengedit data
            return '
                <form method="post">
                    <label for="uraian">Kelurahan:</label>
                    <input type="text" name="kelurahan" value="' . esc_attr($result->kelurahan) . '"><br>
                    <label for="uraian">Jumlah Posyandu Pratama:</label>
                    <input type="number" name="posyandu_pratama" value="' . esc_attr($result->posyandu_pratama) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Posyandu Madya:</label>
                    <input type="number" name="posyandu_madya" value="' . esc_attr($result->posyandu_madya) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Posyandu Purnama:</label>
                    <input type="number" name="posyandu_purnama" value="' . esc_attr($result->posyandu_purnama) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Posyandu Mandiri:</label>
                    <input type="number" name="posyandu_mandiri" value="' . esc_attr($result->posyandu_mandiri) . '" inputmode="numeric" pattern="\d*"><br>
                    <input type="submit" name="submit" value="Update Data" class="update-button">
                    <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
                </form>';
        } else {
            // Jika data tidak ditemukan
            return '<p>Data tidak ditemukan.</p>';
        }
    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_data_posyandu_menurut_strata', 'edit_data_posyandu_menurut_strata');


function tambah_data_posyandu_menurut_strata() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $ppkbd_database = 'websitekecamatanbintantimur';
    $table_name = $ppkbd_database . '.data_posyandu_menurut_strata';

    if (isset($_POST['submit'])) {
        $kelurahan = sanitize_text_field($_POST['kelurahan']);
        $posyandu_pratama = intval($_POST['posyandu_pratama']);
        $posyandu_madya = intval($_POST['posyandu_madya']);
        $posyandu_purnama = intval($_POST['posyandu_purnama']);
        $posyandu_mandiri = intval($_POST['posyandu_mandiri']);

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_posyandu_menurut_strata',
            array(
                'nomor' => $new_nomor,
                'kelurahan' => $kelurahan,
                'posyandu_pratama' => $posyandu_pratama,
                'posyandu_madya' => $posyandu_madya,
                'posyandu_purnama' => $posyandu_purnama,
                'posyandu_mandiri' => $posyandu_mandiri,
            ),
            array('%d', '%s', '%d', '%d', '%d', '%d')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/data-potensi-dan-informasi/";
        </script>';
        exit;
    }

    return '
        <form method="post">
        <label for="uraian">Kelurahan:</label>
                    <input type="text" name="kelurahan" value="' . esc_attr($result->kelurahan) . '"><br>
                    <label for="uraian">Jumlah Posyandu Pratama:</label>
                    <input type="number" name="posyandu_pratama" value="' . esc_attr($result->posyandu_pratama) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Posyandu Madya:</label>
                    <input type="number" name="posyandu_madya" value="' . esc_attr($result->posyandu_madya) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Posyandu Purnama:</label>
                    <input type="number" name="posyandu_purnama" value="' . esc_attr($result->posyandu_purnama) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Posyandu Mandiri:</label>
                    <input type="number" name="posyandu_mandiri" value="' . esc_attr($result->posyandu_mandiri) . '" inputmode="numeric" pattern="\d*"><br>
        <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_posyandu_menurut_strata', 'tambah_data_posyandu_menurut_strata');


function  data_tenaga_medis() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_tenaga_medis';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
            text-align: left;
        }
        .custom-table th{
            font-size: 10px;
        }    
        .custom-table td{
            font-size: 10px;
        }  
        .custom-table th, .custom-table td {
            padding: 12px;
        }
        .custom-table thead {
            background-color: #f2f2f2;
            color: #333;
        }
        .custom-table tr {
            border-bottom: 1px solid #ddd;
        }
        /* Media Query untuk layar kecil */
        @media (max-width: 768px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font */
            }
        }
        @media (max-width: 480px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font lebih kecil */
            }
        }
        </style>';

        $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
        $output .= '<table class="custom-table">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Unit Kerja</th>';
        $output .= '<th style="padding:12px;">Dokter Umum LK</th>';
        $output .= '<th style="padding:12px;">Dokter Umum PR</th>';
        $output .= '<th style="padding:12px;">Dokter Gigi LK</th>';
        $output .= '<th style="padding:12px;">Dokter Gigi PR</th>';
        $output .= '<th style="padding:12px;">Bidan LK</th>';
        $output .= '<th style="padding:12px;">Bidan PR</th>';
        $output .= '<th style="padding:12px;">Perawat LK</th>';
        $output .= '<th style="padding:12px;">Perawat PR</th>';
                                    // Tambahkan kolom "Aksi" jika admin login
                                    if (current_user_can('administrator')) {
                                        $output .= '<th style="padding:12px;">Aksi</th>';
                                    }
        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->unit_kerja) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->dokter_umum_lk) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->dokter_umum_pr) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->dokter_gigi_lk) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->dokter_gigi_pr) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->bidan_lk) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->bidan_pr) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->perawat_lk) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->perawat_pr) . '</td>';
                                    // Jika admin login, tampilkan tombol Edit
                                    if (current_user_can('administrator')) {
                                        $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-data-jumlah-tenaga-medis-dan-keperawatan-di-sarana-kesehatan?id=' . $row->id . '">Edit</a></td>';
                        
                                    }
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }

    if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-data-jumlah-tenaga-medis-dan-keperawatan-di-sarana-kesehatan" class="tambah-button">Tambah Data Jumlah Tenaga Medis Dan Keperawatan Di Sarana Kesehatan</a>';
        $output .= '</div>';
    }

    return $output;
}

function edit_data_tenaga_medis() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $ppkbd_database = 'websitekecamatanbintantimur';
    $table_name = $ppkbd_database . '.data_tenaga_medis'; // ppkbd tabel yang benar

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_tenaga_medis',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/data-potensi-dan-informasi/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
            // Jika form disubmit untuk update
            if (isset($_POST['submit'])) {
                // Validasi dan sanitasi input dari form
                $unit_kerja = sanitize_text_field($_POST['unit_kerja']);
                $dokter_umum_lk = intval($_POST['dokter_umum_lk']);
                $dokter_umum_pr = intval($_POST['dokter_umum_pr']);
                $dokter_gigi_lk = intval($_POST['dokter_gigi_lk']);
                $dokter_gigi_pr = intval($_POST['dokter_gigi_pr']);
                $bidan_lk = intval($_POST['bidan_lk']);
                $bidan_pr = intval($_POST['bidan_pr']);
                $perawat_lk = intval($_POST['perawat_lk']);
                $perawat_pr = intval($_POST['perawat_pr']);

                // Update data ke database
                $wpdb->update(
                    'data_tenaga_medis',
                    array(
                        'unit_kerja' => $unit_kerja,
                        'dokter_umum_lk' => $dokter_umum_lk,
                        'dokter_umum_pr' => $dokter_umum_pr,
                        'dokter_gigi_lk' => $dokter_gigi_lk,
                        'dokter_gigi_pr' => $dokter_gigi_pr,
                        'bidan_lk' => $bidan_lk,
                        'bidan_pr' => $bidan_pr,
                        'perawat_lk' => $perawat_lk,
                        'perawat_pr' => $perawat_pr,
                    ),
                    array('id' => $id),  // Syarat untuk update berdasarkan ID
                    array('%s', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d'),  // Format tipe data
                    array('%d')  // Format untuk ID
                );

                // Redirect setelah data berhasil diupdate
                echo '<script type="text/javascript">
                    alert("Data berhasil diupdate.");
                    window.location.href = "http://websitekecamatanbintantimur.test/data-potensi-dan-informasi/";
                </script>';
                exit;
            }

            // Tampilkan form untuk mengedit data
            return '
                <form method="post">
                    <label for="uraian">Nama Unit Kerja:</label>
                    <input type="text" name="unit_kerja" value="' . esc_attr($result->unit_kerja) . '"><br>
                    <label for="uraian">Jumlah Dokter Umum Laki-Laki:</label>
                    <input type="number" name="dokter_umum_lk" value="' . esc_attr($result->dokter_umum_lk) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Dokter Umum Perempuan:</label>
                    <input type="number" name="dokter_umum_pr" value="' . esc_attr($result->dokter_umum_pr) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Dokter Gigi Laki-Laki:</label>
                    <input type="number" name="dokter_gigi_lk" value="' . esc_attr($result->dokter_gigi_lk) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Dokter Gigi Perempuan:</label>
                    <input type="number" name="dokter_gigi_pr" value="' . esc_attr($result->dokter_gigi_pr) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Bidan Laki-Laki:</label>
                    <input type="number" name="bidan_lk" value="' . esc_attr($result->bidan_lk) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Bidan Perempuan:</label>
                    <input type="number" name="bidan_pr" value="' . esc_attr($result->bidan_pr) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Perawat Laki-Laki:</label>
                    <input type="number" name="perawat_lk" value="' . esc_attr($result->perawat_lk) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Perawat Perempuan:</label>
                    <input type="number" name="perawat_pr" value="' . esc_attr($result->perawat_pr) . '" inputmode="numeric" pattern="\d*"><br>
                    <input type="submit" name="submit" value="Update Data" class="update-button">
                    <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
                </form>';
        } else {
            // Jika data tidak ditemukan
            return '<p>Data tidak ditemukan.</p>';
        }
    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_data_tenaga_medis', 'edit_data_tenaga_medis');


function tambah_data_tenaga_medis() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $ppkbd_database = 'websitekecamatanbintantimur';
    $table_name = $ppkbd_database . '.data_tenaga_medis';

    if (isset($_POST['submit'])) {
        $unit_kerja = sanitize_text_field($_POST['unit_kerja']);
        $dokter_umum_lk = intval($_POST['dokter_umum_lk']);
        $dokter_umum_pr = intval($_POST['dokter_umum_pr']);
        $dokter_gigi_lk = intval($_POST['dokter_gigi_lk']);
        $dokter_gigi_pr = intval($_POST['dokter_gigi_pr']);
        $bidan_lk = intval($_POST['bidan_lk']);
        $bidan_pr = intval($_POST['bidan_pr']);
        $perawat_lk = intval($_POST['perawat_lk']);
        $perawat_pr = intval($_POST['perawat_pr']);

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_tenaga_medis',
            array(
                'nomor' => $new_nomor,
                'unit_kerja' => $unit_kerja,
                'dokter_umum_lk' => $dokter_umum_lk,
                'dokter_umum_pr' => $dokter_umum_pr,
                'dokter_gigi_lk' => $dokter_gigi_lk,
                'dokter_gigi_pr' => $dokter_gigi_pr,
                'bidan_lk' => $bidan_lk,
                'bidan_pr' => $bidan_pr,
                'perawat_lk' => $perawat_lk,
                'perawat_pr' => $perawat_pr,
            ),
            array('%s', '%s', '%d', '%d', '%d', '%d', '%d', '%d', '%d')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/data-potensi-dan-informasi/";
        </script>';
        exit;
    }

    return '
        <form method="post">
                    <label for="uraian">Nama Unit Kerja:</label>
                    <input type="text" name="unit_kerja" value="' . esc_attr($result->unit_kerja) . '"><br>
                    <label for="uraian">Jumlah Dokter Umum Laki-Laki:</label>
                    <input type="number" name="dokter_umum_lk" value="' . esc_attr($result->dokter_umum_lk) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Dokter Umum Perempuan:</label>
                    <input type="number" name="dokter_umum_pr" value="' . esc_attr($result->dokter_umum_pr) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Dokter Gigi Laki-Laki:</label>
                    <input type="number" name="dokter_gigi_lk" value="' . esc_attr($result->dokter_gigi_lk) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Dokter Gigi Perempuan:</label>
                    <input type="number" name="dokter_gigi_pr" value="' . esc_attr($result->dokter_gigi_pr) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Bidan Laki-Laki:</label>
                    <input type="number" name="bidan_lk" value="' . esc_attr($result->bidan_lk) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Bidan Perempuan:</label>
                    <input type="number" name="bidan_pr" value="' . esc_attr($result->bidan_pr) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Perawat Laki-Laki:</label>
                    <input type="number" name="perawat_lk" value="' . esc_attr($result->perawat_lk) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Perawat Perempuan:</label>
                    <input type="number" name="perawat_pr" value="' . esc_attr($result->perawat_pr) . '" inputmode="numeric" pattern="\d*"><br>
        <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_tenaga_medis', 'tambah_data_tenaga_medis');


function   data_kasus_demam_berdarah() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_kasus_demam_berdarah';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
            text-align: left;
        }
        .custom-table th{
            font-size: 10px;
        }    
        .custom-table td{
            font-size: 10px;
        }  
        .custom-table th, .custom-table td {
            padding: 12px;
        }
        .custom-table thead {
            background-color: #f2f2f2;
            color: #333;
        }
        .custom-table tr {
            border-bottom: 1px solid #ddd;
        }
        /* Media Query untuk layar kecil */
        @media (max-width: 768px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font */
            }
        }
        @media (max-width: 480px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font lebih kecil */
            }
        }
        </style>';

        $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
        $output .= '<table class="custom-table">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Kelurahan</th>';
        $output .= '<th style="padding:12px;">Jumlah Kasus LK</th>';
        $output .= '<th style="padding:12px;">Jumlah Kasus PR</th>';
        $output .= '<th style="padding:12px;">Jumlah Meninggal LK</th>';
        $output .= '<th style="padding:12px;">Jumlah Meninggal PR</th>';
                                    // Tambahkan kolom "Aksi" jika admin login
                                    if (current_user_can('administrator')) {
                                        $output .= '<th style="padding:12px;">Aksi</th>';
                                    }

        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->kelurahan) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->jumlah_kasus_lk) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->jumlah_kasus_pr) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->jumlah_meninggal_lk) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->jumlah_meninggal_pr) . '</td>';
                                    // Jika admin login, tampilkan tombol Edit
                                    if (current_user_can('administrator')) {
                                        $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-data-jumlah-kasus-demam-berdarah-menurut-jenis-kelamin?id=' . $row->id . '">Edit</a></td>';
                        
                                    }
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }
    if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-data-jumlah-kasus-demam-berdarah-menurut-jenis-kelamin" class="tambah-button">Tambah Data Jumlah Kasus Demam Berdarah Menurut Jenis Kelamin</a>';
        $output .= '</div>';
    }

    return $output;
}

function edit_data_kasus_demam_berdarah() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $ppkbd_database = 'websitekecamatanbintantimur';
    $table_name = $ppkbd_database . '.data_kasus_demam_berdarah'; // ppkbd tabel yang benar

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_kasus_demam_berdarah',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/data-potensi-dan-informasi/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
            // Jika form disubmit untuk update
            if (isset($_POST['submit'])) {
                // Validasi dan sanitasi input dari form
                $kelurahan = sanitize_text_field($_POST['kelurahan']);
                $jumlah_kasus_lk = intval($_POST['jumlah_kasus_lk']);
                $jumlah_kasus_pr = intval($_POST['jumlah_kasus_pr']);
                $jumlah_meninggal_lk = intval($_POST['jumlah_meninggal_lk']);
                $jumlah_meninggal_pr = intval($_POST['jumlah_meninggal_pr']);


                // Update data ke database
                $wpdb->update(
                    'data_kasus_demam_berdarah',
                    array(
                        'kelurahan' => $kelurahan,
                        'jumlah_kasus_lk' => $jumlah_kasus_lk,
                        'jumlah_kasus_pr' => $jumlah_kasus_pr,
                        'jumlah_meninggal_lk' => $jumlah_meninggal_lk,
                        'jumlah_meninggal_pr' => $jumlah_meninggal_pr,
                    ),
                    array('id' => $id),  // Syarat untuk update berdasarkan ID
                    array('%s', '%d', '%d', '%d', '%d'),  // Format tipe data
                    array('%d')  // Format untuk ID
                );

                // Redirect setelah data berhasil diupdate
                echo '<script type="text/javascript">
                    alert("Data berhasil diupdate.");
                    window.location.href = "http://websitekecamatanbintantimur.test/data-potensi-dan-informasi/";
                </script>';
                exit;
            }

            // Tampilkan form untuk mengedit data
            return '
                <form method="post">
                    <label for="uraian">Kelurahan:</label>
                    <input type="text" name="kelurahan" value="' . esc_attr($result->kelurahan) . '"><br>
                    <label for="uraian">Jumlah Kasus Laki-Laki:</label>
                    <input type="number" name="jumlah_kasus_lk" value="' . esc_attr($result->jumlah_kasus_lk) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Kasus Perempuan:</label>
                    <input type="number" name="jumlah_kasus_pr" value="' . esc_attr($result->jumlah_kasus_pr) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Korban Meninggal Laki-Laki:</label>
                    <input type="number" name="jumlah_meninggal_lk" value="' . esc_attr($result->jumlah_meninggal_lk) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Korban Meninggal Perempuan:</label>
                    <input type="number" name="jumlah_meninggal_pr" value="' . esc_attr($result->jumlah_meninggal_pr) . '" inputmode="numeric" pattern="\d*"><br>
                    <input type="submit" name="submit" value="Update Data" class="update-button">
                    <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
                </form>';
        } else {
            // Jika data tidak ditemukan
            return '<p>Data tidak ditemukan.</p>';
        }
    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_data_kasus_demam_berdarah', 'edit_data_kasus_demam_berdarah');


function tambah_data_kasus_demam_berdarah() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $ppkbd_database = 'websitekecamatanbintantimur';
    $table_name = $ppkbd_database . '.data_kasus_demam_berdarah';

    if (isset($_POST['submit'])) {
        $kelurahan = sanitize_text_field($_POST['kelurahan']);
        $jumlah_kasus_lk = intval($_POST['jumlah_kasus_lk']);
        $jumlah_kasus_pr = intval($_POST['jumlah_kasus_pr']);
        $jumlah_meninggal_lk = intval($_POST['jumlah_meninggal_lk']);
        $jumlah_meninggal_pr = intval($_POST['jumlah_meninggal_pr']);

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_kasus_demam_berdarah',
            array(
                'nomor' => $new_nomor,
                'kelurahan' => $kelurahan,
                        'jumlah_kasus_lk' => $jumlah_kasus_lk,
                        'jumlah_kasus_pr' => $jumlah_kasus_pr,
                        'jumlah_meninggal_lk' => $jumlah_meninggal_lk,
                        'jumlah_meninggal_pr' => $jumlah_meninggal_pr,
            ),
            array('%d', '%s', '%d', '%d', '%d', '%d')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/data-potensi-dan-informasi/";
        </script>';
        exit;
    }

    return '
        <form method="post">
        <label for="uraian">Kelurahan:</label>
                    <input type="text" name="kelurahan" value="' . esc_attr($result->kelurahan) . '"><br>
                    <label for="uraian">Jumlah Kasus Laki-Laki:</label>
                    <input type="number" name="jumlah_kasus_lk" value="' . esc_attr($result->jumlah_kasus_lk) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Kasus Perempuan:</label>
                    <input type="number" name="jumlah_kasus_pr" value="' . esc_attr($result->jumlah_kasus_pr) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Korban Meninggal Laki-Laki:</label>
                    <input type="number" name="jumlah_meninggal_lk" value="' . esc_attr($result->jumlah_meninggal_lk) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Korban Meninggal Perempuan:</label>
                    <input type="number" name="jumlah_meninggal_pr" value="' . esc_attr($result->jumlah_meninggal_pr) . '" inputmode="numeric" pattern="\d*"><br>
        <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_kasus_demam_berdarah', 'tambah_data_kasus_demam_berdarah');

function   data_kasus_malaria() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_kasus_malaria';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
            text-align: left;
        }
        .custom-table th{
            font-size: 10px;
        }    
        .custom-table td{
            font-size: 10px;
        }  
        .custom-table th, .custom-table td {
            padding: 12px;
        }
        .custom-table thead {
            background-color: #f2f2f2;
            color: #333;
        }
        .custom-table tr {
            border-bottom: 1px solid #ddd;
        }
        /* Media Query untuk layar kecil */
        @media (max-width: 768px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font */
            }
        }
        @media (max-width: 480px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font lebih kecil */
            }
        }
        </style>';

        $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
        $output .= '<table class="custom-table">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Kelurahan</th>';
        $output .= '<th style="padding:12px;">Suspek LK</th>';
        $output .= '<th style="padding:12px;">Suspek PR</th>';
        $output .= '<th style="padding:12px;">Meninggal LK</th>';
        $output .= '<th style="padding:12px;">Meninggal PR</th>';
                                    // Tambahkan kolom "Aksi" jika admin login
                                    if (current_user_can('administrator')) {
                                        $output .= '<th style="padding:12px;">Aksi</th>';
                                    }

        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->kelurahan) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->suspek_lk) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->suspek_pr) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->jumlah_meninggal_lk) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->jumlah_meninggal_pr) . '</td>';
                                                // Jika admin login, tampilkan tombol Edit
                                                if (current_user_can('administrator')) {
                                                    $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-data-jumlah-kasus-malaria-menurut-jenis-kelamin?id=' . $row->id . '">Edit</a></td>';
                                    
                                                }
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }
    if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-data-jumlah-kasus-malaria-menurut-jenis-kelamin" class="tambah-button">Tambah Data Jumlah Kasus Malaria Menurut Jenis Kelamin</a>';
        $output .= '</div>';
    }

    return $output;
}

function edit_data_kasus_malaria() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $ppkbd_database = 'websitekecamatanbintantimur';
    $table_name = $ppkbd_database . '.data_kasus_malaria'; // ppkbd tabel yang benar

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_kasus_malaria',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/data-potensi-dan-informasi/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
            // Jika form disubmit untuk update
            if (isset($_POST['submit'])) {
                // Validasi dan sanitasi input dari form
                $kelurahan = sanitize_text_field($_POST['kelurahan']);
                $suspek_lk = intval($_POST['suspek_lk']);
                $suspek_pr = intval($_POST['suspek_pr']);
                $jumlah_meninggal_lk = intval($_POST['jumlah_meninggal_lk']);
                $jumlah_meninggal_pr = intval($_POST['jumlah_meninggal_pr']);


                // Update data ke database
                $wpdb->update(
                    'data_kasus_malaria',
                    array(
                        'kelurahan' => $kelurahan,
                        'suspek_lk' => $suspek_lk,
                        'suspek_pr' => $suspek_pr,
                        'jumlah_meninggal_lk' => $jumlah_meninggal_lk,
                        'jumlah_meninggal_pr' => $jumlah_meninggal_pr,
                    ),
                    array('id' => $id),  // Syarat untuk update berdasarkan ID
                    array('%s', '%d', '%d', '%d', '%d'),  // Format tipe data
                    array('%d')  // Format untuk ID
                );

                // Redirect setelah data berhasil diupdate
                echo '<script type="text/javascript">
                    alert("Data berhasil diupdate.");
                    window.location.href = "http://websitekecamatanbintantimur.test/data-potensi-dan-informasi/";
                </script>';
                exit;
            }

            // Tampilkan form untuk mengedit data
            return '
                <form method="post">
                    <label for="uraian">Kelurahan:</label>
                    <input type="text" name="kelurahan" value="' . esc_attr($result->kelurahan) . '"><br>
                    <label for="uraian">Jumlah Suspek Laki-Laki:</label>
                    <input type="number" name="suspek_lk" value="' . esc_attr($result->suspek_lk) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Suspek Perempuan:</label>
                    <input type="number" name="suspek_pr" value="' . esc_attr($result->suspek_pr) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Korban Meninggal Laki-Laki:</label>
                    <input type="number" name="jumlah_meninggal_lk" value="' . esc_attr($result->jumlah_meninggal_lk) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Korban Meninggal Perempuan:</label>
                    <input type="number" name="jumlah_meninggal_pr" value="' . esc_attr($result->jumlah_meninggal_pr) . '" inputmode="numeric" pattern="\d*"><br>
                    <input type="submit" name="submit" value="Update Data" class="update-button">
                    <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
                </form>';
        } else {
            // Jika data tidak ditemukan
            return '<p>Data tidak ditemukan.</p>';
        }
    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_data_kasus_malaria', 'edit_data_kasus_malaria');


function tambah_data_kasus_malaria() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $ppkbd_database = 'websitekecamatanbintantimur';
    $table_name = $ppkbd_database . '.data_kasus_malaria';

    if (isset($_POST['submit'])) {
        $kelurahan = sanitize_text_field($_POST['kelurahan']);
        $suspek_lk = intval($_POST['suspek_lk']);
        $suspek_pr = intval($_POST['suspek_pr']);
        $jumlah_meninggal_lk = intval($_POST['jumlah_meninggal_lk']);
        $jumlah_meninggal_pr = intval($_POST['jumlah_meninggal_pr']);

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_kasus_malaria',
            array(
                'nomor' => $new_nomor,
                'kelurahan' => $kelurahan,
                        'suspek_lk' => $suspek_lk,
                        'suspek_pr' => $suspek_pr,
                        'jumlah_meninggal_lk' => $jumlah_meninggal_lk,
                        'jumlah_meninggal_pr' => $jumlah_meninggal_pr,
            ),
            array('%d', '%s', '%d', '%d', '%d', '%d')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/data-potensi-dan-informasi/";
        </script>';
        exit;
    }

    return '
        <form method="post">
        <label for="uraian">Kelurahan:</label>
                    <input type="text" name="kelurahan" value="' . esc_attr($result->kelurahan) . '"><br>
                    <label for="uraian">Jumlah Suspek Laki-Laki:</label>
                    <input type="number" name="suspek_lk" value="' . esc_attr($result->suspek_lk) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Suspek Perempuan:</label>
                    <input type="number" name="suspek_pr" value="' . esc_attr($result->suspek_pr) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Korban Meninggal Laki-Laki:</label>
                    <input type="number" name="jumlah_meninggal_lk" value="' . esc_attr($result->jumlah_meninggal_lk) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Korban Meninggal Perempuan:</label>
                    <input type="number" name="jumlah_meninggal_pr" value="' . esc_attr($result->jumlah_meninggal_pr) . '" inputmode="numeric" pattern="\d*"><br>
        <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_kasus_malaria', 'tambah_data_kasus_malaria');


function   data_sarana_ibadah() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_sarana_ibadah';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
            text-align: left;
        }
        .custom-table th{
            font-size: 10px;
        }    
        .custom-table td{
            font-size: 10px;
        }  
        .custom-table th, .custom-table td {
            padding: 12px;
        }
        .custom-table thead {
            background-color: #f2f2f2;
            color: #333;
        }
        .custom-table tr {
            border-bottom: 1px solid #ddd;
        }
        /* Media Query untuk layar kecil */
        @media (max-width: 768px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font */
            }
        }
        @media (max-width: 480px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font lebih kecil */
            }
        }
        </style>';

        $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
        $output .= '<table class="custom-table">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Kelurahan</th>';
        $output .= '<th style="padding:12px;">Masjid</th>';
        $output .= '<th style="padding:12px;">Suran</th>';
        $output .= '<th style="padding:12px;">Gereja Katolik</th>';
        $output .= '<th style="padding:12px;">Gereja Kristen</th>';
        $output .= '<th style="padding:12px;">Vihera/Kelenteng</th>';
        $output .= '<th style="padding:12px;">Pura</th>';
                                    // Tambahkan kolom "Aksi" jika admin login
                                    if (current_user_can('administrator')) {
                                        $output .= '<th style="padding:12px;">Aksi</th>';
                                    }
        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->kelurahan) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->masjid) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->suran) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->gereja_katolik) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->gereja_kristen) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->vihera) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->pura) . '</td>';
                                    // Jika admin login, tampilkan tombol Edit
                                    if (current_user_can('administrator')) {
                                        $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-data-jumlah-sarana-ibadah?id=' . $row->id . '">Edit</a></td>';
                        
                                    }
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }
        if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-data-jumlah-sarana-ibadah" class="tambah-button">Tambah Data Jumlah Sarana Ibadah</a>';
        $output .= '</div>';
    }

    return $output;
}

function edit_data_sarana_ibadah() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $ppkbd_database = 'websitekecamatanbintantimur';
    $table_name = $ppkbd_database . '.data_sarana_ibadah'; // ppkbd tabel yang benar

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_sarana_ibadah',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/data-potensi-dan-informasi/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
            // Jika form disubmit untuk update
            if (isset($_POST['submit'])) {
                // Validasi dan sanitasi input dari form
                $kelurahan = sanitize_text_field($_POST['kelurahan']);
                $masjid = intval($_POST['masjid']);
                $suran = intval($_POST['suran']);
                $gereja_katolik = intval($_POST['gereja_katolik']);
                $gereja_kristen = intval($_POST['gereja_kristen']);
                $vihera = intval($_POST['vihera']);
                $pura = intval($_POST['pura']);

                // Update data ke database
                $wpdb->update(
                    'data_sarana_ibadah',
                    array(
                        'kelurahan' => $kelurahan,
                        'masjid' => $masjid,
                        'suran' => $suran,
                        'gereja_katolik' => $gereja_katolik,
                        'gereja_kristen' => $gereja_kristen,
                        'vihera' => $vihera,
                        'pura' => $pura,
                    ),
                    array('id' => $id),  // Syarat untuk update berdasarkan ID
                    array('%s', '%d', '%d', '%d', '%d', '%d', '%d'),  // Format tipe data
                    array('%d')  // Format untuk ID
                );

                // Redirect setelah data berhasil diupdate
                echo '<script type="text/javascript">
                    alert("Data berhasil diupdate.");
                    window.location.href = "http://websitekecamatanbintantimur.test/data-potensi-dan-informasi/";
                </script>';
                exit;
            }

            // Tampilkan form untuk mengedit data
            return '
                <form method="post">
                    <label for="uraian">Kelurahan:</label>
                    <input type="text" name="kelurahan" value="' . esc_attr($result->kelurahan) . '"><br>
                    <label for="uraian">Jumlah Masjid:</label>
                    <input type="number" name="masjid" value="' . esc_attr($result->masjid) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Suran:</label>
                    <input type="number" name="suran" value="' . esc_attr($result->suran) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Gereja Katolik:</label>
                    <input type="number" name="gereja_katolik" value="' . esc_attr($result->gereja_katolik) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Gereja Kristen:</label>
                    <input type="number" name="gereja_kristen" value="' . esc_attr($result->gereja_kristen) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Vihera/Kelenteng:</label>
                    <input type="number" name="vihera" value="' . esc_attr($result->vihera) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Pura:</label>
                    <input type="number" name="pura" value="' . esc_attr($result->pura) . '" inputmode="numeric" pattern="\d*"><br>
                    <input type="submit" name="submit" value="Update Data" class="update-button">
                    <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
                </form>';
        } else {
            // Jika data tidak ditemukan
            return '<p>Data tidak ditemukan.</p>';
        }
    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_data_sarana_ibadah', 'edit_data_sarana_ibadah');


function tambah_data_sarana_ibadah() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $ppkbd_database = 'websitekecamatanbintantimur';
    $table_name = $ppkbd_database . '.data_sarana_ibadah';

    if (isset($_POST['submit'])) {
        $kelurahan = sanitize_text_field($_POST['kelurahan']);
        $masjid = intval($_POST['masjid']);
        $suran = intval($_POST['suran']);
        $gereja_katolik = intval($_POST['gereja_katolik']);
        $gereja_kristen = intval($_POST['gereja_kristen']);
        $vihera = intval($_POST['vihera']);
        $pura = intval($_POST['pura']);

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_sarana_ibadah',
            array(
                'nomor' => $new_nomor,
                'kelurahan' => $kelurahan,
                        'masjid' => $masjid,
                        'suran' => $suran,
                        'gereja_katolik' => $gereja_katolik,
                        'gereja_kristen' => $gereja_kristen,
                        'vihera' => $vihera,
                        'pura' => $pura,
            ),
            array('%d', '%s', '%d', '%d', '%d', '%d', '%d', '%d')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/data-potensi-dan-informasi/";
        </script>';
        exit;
    }

    return '
        <form method="post">
        <label for="uraian">Kelurahan:</label>
                    <input type="text" name="kelurahan" value="' . esc_attr($result->kelurahan) . '"><br>
                    <label for="uraian">Jumlah Masjid:</label>
                    <input type="number" name="masjid" value="' . esc_attr($result->masjid) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Suran:</label>
                    <input type="number" name="suran" value="' . esc_attr($result->suran) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Gereja Katolik:</label>
                    <input type="number" name="gereja_katolik" value="' . esc_attr($result->gereja_katolik) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Gereja Kristen:</label>
                    <input type="number" name="gereja_kristen" value="' . esc_attr($result->gereja_kristen) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Vihera/Kelenteng:</label>
                    <input type="number" name="vihera" value="' . esc_attr($result->vihera) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Pura:</label>
                    <input type="number" name="pura" value="' . esc_attr($result->pura) . '" inputmode="numeric" pattern="\d*"><br>
        <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_sarana_ibadah', 'tambah_data_sarana_ibadah');


function   data_nikah_cerai_rujuk_talak() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_nikah_cerai_rujuk_talak';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
            text-align: left;
        }
        .custom-table th{
            font-size: 10px;
        }    
        .custom-table td{
            font-size: 10px;
        }  
        .custom-table th, .custom-table td {
            padding: 12px;
        }
        .custom-table thead {
            background-color: #f2f2f2;
            color: #333;
        }
        .custom-table tr {
            border-bottom: 1px solid #ddd;
        }
        /* Media Query untuk layar kecil */
        @media (max-width: 768px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font */
            }
        }
        @media (max-width: 480px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font lebih kecil */
            }
        }
        </style>';

        $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
        $output .= '<table class="custom-table">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Kelurahan</th>';
        $output .= '<th style="padding:12px;">Nikah</th>';
        $output .= '<th style="padding:12px;">Cerai</th>';
        $output .= '<th style="padding:12px;">Rujuk</th>';
                            // Tambahkan kolom "Aksi" jika admin login
                            if (current_user_can('administrator')) {
                                $output .= '<th style="padding:12px;">Aksi</th>';
                            }
        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->kelurahan) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->nikah) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->cerai) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->rujuk) . '</td>';
                                    // Jika admin login, tampilkan tombol Edit
                        if (current_user_can('administrator')) {
                            $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-data-data-nikah-cerai-rujuk-talak?id=' . $row->id . '">Edit</a></td>';
            
                        }
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }

    if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-data-data-nikah-cerai-rujuk-talak" class="tambah-button">Tambah Data Nikah, Cerai, Rujuk, Talak</a>';
        $output .= '</div>';
    }


    return $output;
}

function edit_data_nikah_cerai_rujuk_talak() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $ppkbd_database = 'websitekecamatanbintantimur';
    $table_name = $ppkbd_database . '.data_nikah_cerai_rujuk_talak'; // ppkbd tabel yang benar

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_nikah_cerai_rujuk_talak',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/data-potensi-dan-informasi/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
            // Jika form disubmit untuk update
            if (isset($_POST['submit'])) {
                // Validasi dan sanitasi input dari form
                $kelurahan = sanitize_text_field($_POST['kelurahan']);
                $nikah = intval($_POST['nikah']);
                $cerai = intval($_POST['cerai']);
                $rujuk = intval($_POST['rujuk']);

                // Update data ke database
                $wpdb->update(
                    'data_nikah_cerai_rujuk_talak',
                    array(
                        'kelurahan' => $kelurahan,
                        'nikah' => $nikah,
                        'cerai' => $cerai,
                        'rujuk' => $rujuk,
                    ),
                    array('id' => $id),  // Syarat untuk update berdasarkan ID
                    array('%s', '%d', '%d', '%d'),  // Format tipe data
                    array('%d')  // Format untuk ID
                );

                // Redirect setelah data berhasil diupdate
                echo '<script type="text/javascript">
                    alert("Data berhasil diupdate.");
                    window.location.href = "http://websitekecamatanbintantimur.test/data-potensi-dan-informasi/";
                </script>';
                exit;
            }

            // Tampilkan form untuk mengedit data
            return '
                <form method="post">
                    <label for="uraian">Kelurahan:</label>
                    <input type="text" name="kelurahan" value="' . esc_attr($result->kelurahan) . '"><br>
                    <label for="uraian">Jumlah Warga Menikah:</label>
                    <input type="number" name="nikah" value="' . esc_attr($result->nikah) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah  Warga Cerai:</label>
                    <input type="number" name="cerai" value="' . esc_attr($result->cerai) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah  Warga Rujuk:</label>
                    <input type="number" name="rujuk" value="' . esc_attr($result->rujuk) . '" inputmode="numeric" pattern="\d*"><br>
                    <input type="submit" name="submit" value="Update Data" class="update-button">
                    <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
                </form>';
        } else {
            // Jika data tidak ditemukan
            return '<p>Data tidak ditemukan.</p>';
        }
    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_data_nikah_cerai_rujuk_talak', 'edit_data_nikah_cerai_rujuk_talak');


function tambah_data_nikah_cerai_rujuk_talak() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $ppkbd_database = 'websitekecamatanbintantimur';
    $table_name = $ppkbd_database . '.data_nikah_cerai_rujuk_talak';

    if (isset($_POST['submit'])) {
        $kelurahan = sanitize_text_field($_POST['kelurahan']);
        $nikah = intval($_POST['nikah']);
        $cerai = intval($_POST['cerai']);
        $rujuk = intval($_POST['rujuk']);

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_nikah_cerai_rujuk_talak',
            array(
                'nomor' => $new_nomor,
                'kelurahan' => $kelurahan,
                        'nikah' => $nikah,
                        'cerai' => $cerai,
                        'rujuk' => $rujuk,
            ),
            array('%d', '%s', '%d', '%d', '%d')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/data-potensi-dan-informasi/";
        </script>';
        exit;
    }

    return '
        <form method="post">
        <label for="uraian">Kelurahan:</label>
                    <input type="text" name="kelurahan" value="' . esc_attr($result->kelurahan) . '"><br>
                    <label for="uraian">Jumlah Warga Menikah:</label>
                    <input type="number" name="nikah" value="' . esc_attr($result->nikah) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah  Warga Cerai:</label>
                    <input type="number" name="cerai" value="' . esc_attr($result->cerai) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah  Warga Rujuk:</label>
                    <input type="number" name="rujuk" value="' . esc_attr($result->rujuk) . '" inputmode="numeric" pattern="\d*"><br>
        <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_nikah_cerai_rujuk_talak', 'tambah_data_nikah_cerai_rujuk_talak');


function   data_sebaran_kesenian_bintan_timur() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_sebaran_kesenian_bintan_timur';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
            text-align: left;
        }
        .custom-table th{
            font-size: 10px;
        }    
        .custom-table td{
            font-size: 10px;
        }  
        .custom-table th, .custom-table td {
            padding: 12px;
        }
        .custom-table thead {
            background-color: #f2f2f2;
            color: #333;
        }
        .custom-table tr {
            border-bottom: 1px solid #ddd;
        }
        /* Media Query untuk layar kecil */
        @media (max-width: 768px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font */
            }
        }
        @media (max-width: 480px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font lebih kecil */
            }
        }
        </style>';

        $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
        $output .= '<table class="custom-table">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Nama Sanggar</th>';
        $output .= '<th style="padding:12px;">Alamat</th>';
        $output .= '<th style="padding:12px;">Kalsifikasi Organisasi</th>';
        $output .= '<th style="padding:12px;">Pimpinan</th>';
        $output .= '<th style="padding:12px;">Ket</th>';
                            // Tambahkan kolom "Aksi" jika admin login
                            if (current_user_can('administrator')) {
                                $output .= '<th style="padding:12px;">Aksi</th>';
                            }
        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->nama_sanggar) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->alamat) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->kalsifikasi_organisasi) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->pimpinan) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->ket) . '</td>';
                                    // Jika admin login, tampilkan tombol Edit
                                    if (current_user_can('administrator')) {
                                        $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-data-jumlah-sebaran-kesenian-bintan-timur?id=' . $row->id . '">Edit</a></td>';
                        
                                    }
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }
        if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-data-jumlah-sebaran-kesenian-bintan-timur" class="tambah-button">Tambah Data Jumlah Sebaran Kesenian Bintan Timur</a>';
        $output .= '</div>';
    }
    return $output;
}

function edit_data_sebaran_kesenian_bintan_timur() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $ppkbd_database = 'websitekecamatanbintantimur';
    $table_name = $ppkbd_database . '.data_sebaran_kesenian_bintan_timur'; // ppkbd tabel yang benar

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_sebaran_kesenian_bintan_timur',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/data-potensi-dan-informasi/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
            // Jika form disubmit untuk update
            if (isset($_POST['submit'])) {
                // Validasi dan sanitasi input dari form
                $nama_sanggar = sanitize_text_field($_POST['nama_sanggar']);
                $alamat = sanitize_text_field($_POST['alamat']);
                $kalsifikasi_organisasi = sanitize_text_field($_POST['kalsifikasi_organisasi']);
                $pimpinan = sanitize_text_field($_POST['pimpinan']);
                $ket = sanitize_text_field($_POST['ket']);

                // Update data ke database
                $wpdb->update(
                    'data_sebaran_kesenian_bintan_timur',
                    array(
                        'nama_sanggar' => $nama_sanggar,
                        'alamat' => $alamat,
                        'kalsifikasi_organisasi' => $kalsifikasi_organisasi,
                        'pimpinan' => $pimpinan,
                        'ket' => $ket,
                    ),
                    array('id' => $id),  // Syarat untuk update berdasarkan ID
                    array('%s', '%s', '%s', '%s', '%s'),  // Format tipe data
                    array('%d')  // Format untuk ID
                );

                // Redirect setelah data berhasil diupdate
                echo '<script type="text/javascript">
                    alert("Data berhasil diupdate.");
                    window.location.href = "http://websitekecamatanbintantimur.test/data-potensi-dan-informasi/";
                </script>';
                exit;
            }

            // Tampilkan form untuk mengedit data
            return '
                <form method="post">
                    <label for="uraian">Nama Sanggar:</label>
                    <input type="text" name="nama_sanggar" value="' . esc_attr($result->nama_sanggar) . '"><br>
                    <label for="uraian">Alamat:</label>
                    <input type="text" name="alamat" value="' . esc_attr($result->alamat) . '"><br>
                    <label for="uraian">Kalsifikasi Organisasi:</label>
                    <input type="text" name="kalsifikasi_organisasi" value="' . esc_attr($result->kalsifikasi_organisasi) . '"><br>
                    <label for="uraian">Pimpinan:</label>
                    <input type="text" name="pimpinan" value="' . esc_attr($result->pimpinan) . '"><br>
                    <label for="uraian">Keterangan:</label>
                    <input type="text" name="ket" value="' . esc_attr($result->ket) . '"><br>
                    <input type="submit" name="submit" value="Update Data" class="update-button">
                    <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
                </form>';
        } else {
            // Jika data tidak ditemukan
            return '<p>Data tidak ditemukan.</p>';
        }
    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_data_sebaran_kesenian_bintan_timur', 'edit_data_sebaran_kesenian_bintan_timur');


function tambah_data_sebaran_kesenian_bintan_timur() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $ppkbd_database = 'websitekecamatanbintantimur';
    $table_name = $ppkbd_database . '.data_sebaran_kesenian_bintan_timur';

    if (isset($_POST['submit'])) {
        $nama_sanggar = sanitize_text_field($_POST['nama_sanggar']);
                $alamat = sanitize_text_field($_POST['alamat']);
                $kalsifikasi_organisasi = sanitize_text_field($_POST['kalsifikasi_organisasi']);
                $pimpinan = sanitize_text_field($_POST['pimpinan']);
                $ket = sanitize_text_field($_POST['ket']);

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_sebaran_kesenian_bintan_timur',
            array(
                'nomor' => $new_nomor,
                'nama_sanggar' => $nama_sanggar,
                        'alamat' => $alamat,
                        'kalsifikasi_organisasi' => $kalsifikasi_organisasi,
                        'pimpinan' => $pimpinan,
                        'ket' => $ket,
            ),
            array('%d', '%s', '%s', '%s', '%s', '%s')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/data-potensi-dan-informasi/";
        </script>';
        exit;
    }

    return '
        <form method="post">
        <label for="uraian">Nama Sanggar:</label>
                    <input type="text" name="nama_sanggar" value="' . esc_attr($result->nama_sanggar) . '"><br>
                    <label for="uraian">Alamat:</label>
                    <input type="text" name="alamat" value="' . esc_attr($result->alamat) . '"><br>
                    <label for="uraian">Kalsifikasi Organisasi:</label>
                    <input type="text" name="kalsifikasi_organisasi" value="' . esc_attr($result->kalsifikasi_organisasi) . '"><br>
                    <label for="uraian">Pimpinan:</label>
                    <input type="text" name="pimpinan" value="' . esc_attr($result->pimpinan) . '"><br>
                    <label for="uraian">Keterangan:</label>
                    <input type="text" name="ket" value="' . esc_attr($result->ket) . '"><br>
        <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_sebaran_kesenian_bintan_timur', 'tambah_data_sebaran_kesenian_bintan_timur');

function   data_pengamanan_pertahanan() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_pengamanan_pertahanan';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
            text-align: left;
        }
        .custom-table th{
            font-size: 10px;
        }    
        .custom-table td{
            font-size: 10px;
        }  
        .custom-table th, .custom-table td {
            padding: 12px;
        }
        .custom-table thead {
            background-color: #f2f2f2;
            color: #333;
        }
        .custom-table tr {
            border-bottom: 1px solid #ddd;
        }
        /* Media Query untuk layar kecil */
        @media (max-width: 768px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font */
            }
        }
        @media (max-width: 480px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font lebih kecil */
            }
        }
        </style>';

        $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
        $output .= '<table class="custom-table">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Kelurahan</th>';
        $output .= '<th style="padding:12px;">Poskamling</th>';
        $output .= '<th style="padding:12px;">Anggota Linmas</th>';
        $output .= '<th style="padding:12px;">Satpam</th>';
                            // Tambahkan kolom "Aksi" jika admin login
                            if (current_user_can('administrator')) {
                                $output .= '<th style="padding:12px;">Aksi</th>';
                            }
        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->kelurahan) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->poskamling) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->anggota_limnas) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->satpam) . '</td>';
                                    // Jika admin login, tampilkan tombol Edit
                        if (current_user_can('administrator')) {
                            $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-data-satuan-pengamanan-dan-pertahanan?id=' . $row->id . '">Edit</a></td>';
            
                        }
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }
    if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-data-satuan-pengamanan-dan-pertahanan" class="tambah-button">Tambah Data Satuan Pengamanan Dan Pertahanan</a>';
        $output .= '</div>';
    }

    return $output;
}

function edit_data_pengamanan_pertahanan() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $ppkbd_database = 'websitekecamatanbintantimur';
    $table_name = $ppkbd_database . '.data_pengamanan_pertahanan'; // ppkbd tabel yang benar

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_pengamanan_pertahanan',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/data-potensi-dan-informasi/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
            // Jika form disubmit untuk update
            if (isset($_POST['submit'])) {
                // Validasi dan sanitasi input dari form
                $kelurahan = sanitize_text_field($_POST['kelurahan']);
                $poskamling = intval($_POST['poskamling']);
                $anggota_limnas = intval($_POST['anggota_limnas']);
                $satpam = intval($_POST['satpam']);

                // Update data ke database
                $wpdb->update(
                    'data_pengamanan_pertahanan',
                    array(
                        'kelurahan' => $kelurahan,
                        'poskamling' => $poskamling,
                        'anggota_limnas' => $anggota_limnas,
                        'satpam' => $satpam,
                    ),
                    array('id' => $id),  // Syarat untuk update berdasarkan ID
                    array('%s', '%d', '%d', '%d'),  // Format tipe data
                    array('%d')  // Format untuk ID
                );

                // Redirect setelah data berhasil diupdate
                echo '<script type="text/javascript">
                    alert("Data berhasil diupdate.");
                    window.location.href = "http://websitekecamatanbintantimur.test/data-potensi-dan-informasi/";
                </script>';
                exit;
            }

            // Tampilkan form untuk mengedit data
            return '
                <form method="post">
                    <label for="uraian">Kelurahan:</label>
                    <input type="text" name="kelurahan" value="' . esc_attr($result->kelurahan) . '"><br>
                    <label for="uraian">Jumlah Poskamling:</label>
                    <input type="number" name="poskamling" value="' . esc_attr($result->poskamling) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Anggota Limnas:</label>
                    <input type="number" name="anggota_limnas" value="' . esc_attr($result->anggota_limnas) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Satpam:</label>
                    <input type="number" name="satpam" value="' . esc_attr($result->satpam) . '" inputmode="numeric" pattern="\d*"><br>
                    <input type="submit" name="submit" value="Update Data" class="update-button">
                    <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
                </form>';
        } else {
            // Jika data tidak ditemukan
            return '<p>Data tidak ditemukan.</p>';
        }
    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_data_pengamanan_pertahanan', 'edit_data_pengamanan_pertahanan');


function tambah_data_pengamanan_pertahanan() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $ppkbd_database = 'websitekecamatanbintantimur';
    $table_name = $ppkbd_database . '.data_pengamanan_pertahanan';

    if (isset($_POST['submit'])) {
        $kelurahan = sanitize_text_field($_POST['kelurahan']);
        $poskamling = intval($_POST['poskamling']);
        $anggota_limnas = intval($_POST['anggota_limnas']);
        $satpam = intval($_POST['satpam']);

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_pengamanan_pertahanan',
            array(
                'nomor' => $new_nomor,
                'kelurahan' => $kelurahan,
                        'poskamling' => $poskamling,
                        'anggota_limnas' => $anggota_limnas,
                        'satpam' => $satpam,
            ),
            array('%d', '%s', '%d', '%d', '%d')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/data-potensi-dan-informasi/";
        </script>';
        exit;
    }

    return '
        <form method="post">
        <label for="uraian">Kelurahan:</label>
                    <input type="text" name="kelurahan" value="' . esc_attr($result->kelurahan) . '"><br>
                    <label for="uraian">Jumlah Poskamling:</label>
                    <input type="number" name="poskamling" value="' . esc_attr($result->poskamling) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Anggota Limnas:</label>
                    <input type="number" name="anggota_limnas" value="' . esc_attr($result->anggota_limnas) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Satpam:</label>
                    <input type="number" name="satpam" value="' . esc_attr($result->satpam) . '" inputmode="numeric" pattern="\d*"><br>
        <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_pengamanan_pertahanan', 'tambah_data_pengamanan_pertahanan');


function   data_kenakalan_remaja() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_kenakalan_remaja';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
            text-align: left;
        }
        .custom-table th{
            font-size: 10px;
        }    
        .custom-table td{
            font-size: 10px;
        }  
        .custom-table th, .custom-table td {
            padding: 12px;
        }
        .custom-table thead {
            background-color: #f2f2f2;
            color: #333;
        }
        .custom-table tr {
            border-bottom: 1px solid #ddd;
        }
        /* Media Query untuk layar kecil */
        @media (max-width: 768px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font */
            }
        }
        @media (max-width: 480px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font lebih kecil */
            }
        }
        </style>';

        $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
        $output .= '<table class="custom-table">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Kelurahan</th>';
        $output .= '<th style="padding:12px;">Penggunaan Obat-Obatan</th>';
        $output .= '<th style="padding:12px;">Perkelahian Remaja</th>';
        $output .= '<th style="padding:12px;">Kebiasaan Mabuk</th>';
        $output .= '<th style="padding:12px;">Pemerkosaan</th>';
                            // Tambahkan kolom "Aksi" jika admin login
                            if (current_user_can('administrator')) {
                                $output .= '<th style="padding:12px;">Aksi</th>';
                            }
        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->kelurahan) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->penggunaan_obat) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->perkelahian) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->mabuk) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->pemerkosaan) . '</td>';
                                    // Jika admin login, tampilkan tombol Edit
                                    if (current_user_can('administrator')) {
                                        $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-data-kenakalan-remaja?id=' . $row->id . '">Edit</a></td>';
                        
                                    }
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }
    if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-data-kenakalan-remaja" class="tambah-button">Tambah Data Kenakalan Remaja</a>';
        $output .= '</div>';
    }
    return $output;
}

function edit_data_kenakalan_remaja() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $ppkbd_database = 'websitekecamatanbintantimur';
    $table_name = $ppkbd_database . '.data_kenakalan_remaja'; // ppkbd tabel yang benar

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_kenakalan_remaja',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/data-potensi-dan-informasi/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
            // Jika form disubmit untuk update
            if (isset($_POST['submit'])) {
                // Validasi dan sanitasi input dari form
                $kelurahan = sanitize_text_field($_POST['kelurahan']);
                $penggunaan_obat = intval($_POST['penggunaan_obat']);
                $perkelahian = intval($_POST['perkelahian']);
                $mabuk = intval($_POST['mabuk']);
                $pemerkosaan = intval($_POST['pemerkosaan']);

                // Update data ke database
                $wpdb->update(
                    'data_kenakalan_remaja',
                    array(
                        'kelurahan' => $kelurahan,
                        'penggunaan_obat' => $penggunaan_obat,
                        'perkelahian' => $perkelahian,
                        'mabuk' => $mabuk,
                        'pemerkosaan' => $pemerkosaan,
                    ),
                    array('id' => $id),  // Syarat untuk update berdasarkan ID
                    array('%s', '%d', '%d', '%d', '%d'),  // Format tipe data
                    array('%d')  // Format untuk ID
                );

                // Redirect setelah data berhasil diupdate
                echo '<script type="text/javascript">
                    alert("Data berhasil diupdate.");
                    window.location.href = "http://websitekecamatanbintantimur.test/data-potensi-dan-informasi/";
                </script>';
                exit;
            }

            // Tampilkan form untuk mengedit data
            return '
                <form method="post">
                    <label for="uraian">Kelurahan:</label>
                    <input type="text" name="kelurahan" value="' . esc_attr($result->kelurahan) . '"><br>
                    <label for="uraian">Jumlah Remaja Penggunaan Obat-Obatan:</label>
                    <input type="number" name="penggunaan_obat" value="' . esc_attr($result->penggunaan_obat) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Perkelahian Antar Remaja:</label>
                    <input type="number" name="perkelahian" value="' . esc_attr($result->perkelahian) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Remaja Kebiasaan Mabuk:</label>
                    <input type="number" name="mabuk" value="' . esc_attr($result->mabuk) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Pemerkosaan Remaja:</label>
                    <input type="number" name="pemerkosaan" value="' . esc_attr($result->pemerkosaan) . '" inputmode="numeric" pattern="\d*"><br>
                    <input type="submit" name="submit" value="Update Data" class="update-button">
                    <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
                </form>';
        } else {
            // Jika data tidak ditemukan
            return '<p>Data tidak ditemukan.</p>';
        }
    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_data_kenakalan_remaja', 'edit_data_kenakalan_remaja');


function tambah_data_kenakalan_remaja() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $ppkbd_database = 'websitekecamatanbintantimur';
    $table_name = $ppkbd_database . '.data_kenakalan_remaja';

    if (isset($_POST['submit'])) {
        $kelurahan = sanitize_text_field($_POST['kelurahan']);
                $penggunaan_obat = intval($_POST['penggunaan_obat']);
                $perkelahian = intval($_POST['perkelahian']);
                $mabuk = intval($_POST['mabuk']);
                $pemerkosaan = intval($_POST['pemerkosaan']);

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_kenakalan_remaja',
            array(
                'nomor' => $new_nomor,
                'kelurahan' => $kelurahan,
                        'penggunaan_obat' => $penggunaan_obat,
                        'perkelahian' => $perkelahian,
                        'mabuk' => $mabuk,
                        'pemerkosaan' => $pemerkosaan,
            ),
            array('%d', '%s', '%d', '%d', '%d', '%d')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/data-potensi-dan-informasi/";
        </script>';
        exit;
    }

    return '
        <form method="post">
        <label for="uraian">Kelurahan:</label>
                    <input type="text" name="kelurahan" value="' . esc_attr($result->kelurahan) . '"><br>
                    <label for="uraian">Jumlah Remaja Penggunaan Obat-Obatan:</label>
                    <input type="number" name="penggunaan_obat" value="' . esc_attr($result->penggunaan_obat) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Perkelahian Antar Remaja:</label>
                    <input type="number" name="perkelahian" value="' . esc_attr($result->perkelahian) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Remaja Kebiasaan Mabuk:</label>
                    <input type="number" name="mabuk" value="' . esc_attr($result->mabuk) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Pemerkosaan Remaja:</label>
                    <input type="number" name="pemerkosaan" value="' . esc_attr($result->pemerkosaan) . '" inputmode="numeric" pattern="\d*"><br>
        <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_kenakalan_remaja', 'tambah_data_kenakalan_remaja');

function   data_pelanggaran_hukum() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_pelanggaran_hukum';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
            text-align: left;
        }
        .custom-table th{
            font-size: 10px;
        }    
        .custom-table td{
            font-size: 10px;
        }  
        .custom-table th, .custom-table td {
            padding: 12px;
        }
        .custom-table thead {
            background-color: #f2f2f2;
            color: #333;
        }
        .custom-table tr {
            border-bottom: 1px solid #ddd;
        }
        /* Media Query untuk layar kecil */
        @media (max-width: 768px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font */
            }
        }
        @media (max-width: 480px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font lebih kecil */
            }
        }
        </style>';

        $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
        $output .= '<table class="custom-table">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Uraian</th>';
        $output .= '<th style="padding:12px;">Kijang kota</th>';
        $output .= '<th style="padding:12px;">Sungai Enam</th>';
        $output .= '<th style="padding:12px;">Gunung Lengkuas</th>';
        $output .= '<th style="padding:12px;">Sungai Lekop</th>';
                            // Tambahkan kolom "Aksi" jika admin login
                            if (current_user_can('administrator')) {
                                $output .= '<th style="padding:12px;">Aksi</th>';
                            }
        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->uraian) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->kijang_kota) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->sungai_enam) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->gunung_lengkuas) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->sungai_lekop) . '</td>';
                                    // Jika admin login, tampilkan tombol Edit
                        if (current_user_can('administrator')) {
                            $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-data-pelanggaran-hukum?id=' . $row->id . '">Edit</a></td>';
            
                        }
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }
    if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-data-pelanggaran-hukum" class="tambah-button">Tambah Data Pelanggaran Hukum (Peristiwa Pidana)</a>';
        $output .= '</div>';
    }
    return $output;
}

function edit_data_pelanggaran_hukum() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $ppkbd_database = 'websitekecamatanbintantimur';
    $table_name = $ppkbd_database . '.data_pelanggaran_hukum'; // ppkbd tabel yang benar

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_pelanggaran_hukum',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/data-potensi-dan-informasi/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
            // Jika form disubmit untuk update
            if (isset($_POST['submit'])) {
                // Validasi dan sanitasi input dari form
                $uraian = sanitize_text_field($_POST['uraian']);
                $kijang_kota = intval($_POST['kijang_kota']);
                $sungai_enam = intval($_POST['sungai_enam']);
                $gunung_lengkuas = intval($_POST['gunung_lengkuas']);
                $sungai_lekop = intval($_POST['sungai_lekop']);

                // Update data ke database
                $wpdb->update(
                    'data_pelanggaran_hukum',
                    array(
                        'uraian' => $uraian,
                        'kijang_kota' => $kijang_kota,
                        'sungai_enam' => $sungai_enam,
                        'gunung_lengkuas' => $gunung_lengkuas,
                        'sungai_lekop' => $sungai_lekop,
                    ),
                    array('id' => $id),  // Syarat untuk update berdasarkan ID
                    array('%s', '%d', '%d', '%d', '%d'),  // Format tipe data
                    array('%d')  // Format untuk ID
                );

                // Redirect setelah data berhasil diupdate
                echo '<script type="text/javascript">
                    alert("Data berhasil diupdate.");
                    window.location.href = "http://websitekecamatanbintantimur.test/data-potensi-dan-informasi/";
                </script>';
                exit;
            }

            // Tampilkan form untuk mengedit data
            return '
                <form method="post">
                    <label for="uraian">Uraian:</label>
                    <input type="text" name="uraian" value="' . esc_attr($result->uraian) . '"><br>
                    <label for="uraian">Jumlah Pelanggaran Di Kijang Kota:</label>
                    <input type="number" name="kijang_kota" value="' . esc_attr($result->kijang_kota) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Pelanggaran Di Sungai Enam:</label>
                    <input type="number" name="sungai_enam" value="' . esc_attr($result->sungai_enam) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Pelanggaran Di Gunung Lengkuas:</label>
                    <input type="number" name="gunung_lengkuas" value="' . esc_attr($result->gunung_lengkuas) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Pelanggaran Di Sungai Lekop:</label>
                    <input type="number" name="sungai_lekop" value="' . esc_attr($result->sungai_lekop) . '" inputmode="numeric" pattern="\d*"><br>
                    <input type="submit" name="submit" value="Update Data" class="update-button">
                    <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
                </form>';
        } else {
            // Jika data tidak ditemukan
            return '<p>Data tidak ditemukan.</p>';
        }
    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_data_pelanggaran_hukum', 'edit_data_pelanggaran_hukum');


function tambah_data_pelanggaran_hukum() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $ppkbd_database = 'websitekecamatanbintantimur';
    $table_name = $ppkbd_database . '.data_pelanggaran_hukum';

    if (isset($_POST['submit'])) {
        $uraian = sanitize_text_field($_POST['uraian']);
                $kijang_kota = intval($_POST['kijang_kota']);
                $sungai_enam = intval($_POST['sungai_enam']);
                $gunung_lengkuas = intval($_POST['gunung_lengkuas']);
                $sungai_lekop = intval($_POST['sungai_lekop']);

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_pelanggaran_hukum',
            array(
                'nomor' => $new_nomor,
                'uraian' => $uraian,
                        'kijang_kota' => $kijang_kota,
                        'sungai_enam' => $sungai_enam,
                        'gunung_lengkuas' => $gunung_lengkuas,
                        'sungai_lekop' => $sungai_lekop,
            ),
            array('%d', '%s', '%d', '%d', '%d', '%d')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/data-potensi-dan-informasi/";
        </script>';
        exit;
    }

    return '
        <form method="post">
        <label for="uraian">Uraian:</label>
                    <input type="text" name="uraian" value="' . esc_attr($result->uraian) . '"><br>
                    <label for="uraian">Jumlah Pelanggaran Di Kijang Kota:</label>
                    <input type="number" name="kijang_kota" value="' . esc_attr($result->kijang_kota) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Pelanggaran Di Sungai Enam:</label>
                    <input type="number" name="sungai_enam" value="' . esc_attr($result->sungai_enam) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Pelanggaran Di Gunung Lengkuas:</label>
                    <input type="number" name="gunung_lengkuas" value="' . esc_attr($result->gunung_lengkuas) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Pelanggaran Di Sungai Lekop:</label>
                    <input type="number" name="sungai_lekop" value="' . esc_attr($result->sungai_lekop) . '" inputmode="numeric" pattern="\d*"><br>
        <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_pelanggaran_hukum', 'tambah_data_pelanggaran_hukum');

function   data_bencana_alam() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_bencana_alam';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
            text-align: left;
        }
        .custom-table th{
            font-size: 10px;
        }    
        .custom-table td{
            font-size: 10px;
        }  
        .custom-table th, .custom-table td {
            padding: 12px;
        }
        .custom-table thead {
            background-color: #f2f2f2;
            color: #333;
        }
        .custom-table tr {
            border-bottom: 1px solid #ddd;
        }
        /* Media Query untuk layar kecil */
        @media (max-width: 768px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font */
            }
        }
        @media (max-width: 480px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font lebih kecil */
            }
        }
        </style>';

        $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
        $output .= '<table class="custom-table">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Hari / Tanggal</th>';
        $output .= '<th style="padding:12px;">Jenis Bencana</th>';
        $output .= '<th style="padding:12px;">Lokasi Bencana</th>';
        $output .= '<th style="padding:12px;">Jumlah KK</th>';
        $output .= '<th style="padding:12px;">Jumlah Jiwa</th>';
        $output .= '<th style="padding:12px;">Laki-Laki</th>';
        $output .= '<th style="padding:12px;">Perempuan</th>';
                            // Tambahkan kolom "Aksi" jika admin login
                            if (current_user_can('administrator')) {
                                $output .= '<th style="padding:12px;">Aksi</th>';
                            }
        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            // Misal $row->tanggal memiliki format yyyy-mm-dd dari database
            $tanggal = strtotime($row->tanggal); // Konversi ke timestamp
            $tanggalFormatted = date('d M Y', $tanggal); // Format menjadi dd mm yy

            $output .= '<td style="padding:12px;">' . esc_html($tanggalFormatted) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->jenis_bencana) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->lokasi_bencana) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->jumlah_kk) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->jumlah_jiwa) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->laki) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->perempuan) . '</td>';
                                    // Jika admin login, tampilkan tombol Edit
                                    if (current_user_can('administrator')) {
                                        $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-data-jumlah-bencana-alam-di-kecamatan-bintan-timur-tahun-2022?id=' . $row->id . '">Edit</a></td>';
                        
                                    }
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }
    if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-data-jumlah-bencana-alam-di-kecamatan-bintan-timur-tahun-2022" class="tambah-button">Tambah Data Jumlah Bencana Alam Di Kecamatan Bintan Timur Tahun 2022</a>';
        $output .= '</div>';
    }

    return $output;
}

function edit_data_bencana_alam() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $ppkbd_database = 'websitekecamatanbintantimur';
    $table_name = $ppkbd_database . '.data_bencana_alam'; // ppkbd tabel yang benar

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_bencana_alam',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/data-potensi-dan-informasi/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
            // Jika form disubmit untuk update
            if (isset($_POST['submit'])) {
                // Validasi dan sanitasi input dari form
                $tanggal = date('Y-m-d', strtotime($_POST['tanggal']));
                $jenis_bencana = sanitize_text_field($_POST['jenis_bencana']);
                $lokasi_bencana = sanitize_text_field($_POST['lokasi_bencana']);
                $jumlah_kk = intval($_POST['jumlah_kk']);
                $jumlah_jiwa = intval($_POST['jumlah_jiwa']);
                $laki = intval($_POST['laki']);
                $perempuan = intval($_POST['perempuan']);

                // Update data ke database
                $wpdb->update(
                    'data_bencana_alam',
                    array(
                        'tanggal' => $tanggal,
                        'jenis_bencana' => $jenis_bencana,
                        'lokasi_bencana' => $lokasi_bencana,
                        'jumlah_kk' => $jumlah_kk,
                        'jumlah_jiwa' => $jumlah_jiwa,
                        'laki' => $laki,
                        'perempuan' => $perempuan,
                    ),
                    array('id' => $id),  // Syarat untuk update berdasarkan ID
                    array('%s', '%s', '%s', '%d', '%d', '%d', '%d'),  // Format tipe data
                    array('%d')  // Format untuk ID
                );

                // Redirect setelah data berhasil diupdate
                echo '<script type="text/javascript">
                    alert("Data berhasil diupdate.");
                    window.location.href = "http://websitekecamatanbintantimur.test/data-potensi-dan-informasi/";
                </script>';
                exit;
            }

            // Tampilkan form untuk mengedit data
            return '
                <form method="post">
                    <label for="uraian">Tanggal Kejadian:</label>
                    <input type="date" name="tanggal" value="' . esc_attr($result->tanggal) .'"><br>
                    <label for="uraian">Jenis Bencana:</label>
                    <input type="text" name="jenis_bencana" value="' . esc_attr($result->jenis_bencana) . '"><br>
                    <label for="uraian">Lokasi Bencana:</label>
                    <input type="text" name="lokasi_bencana" value="' . esc_attr($result->lokasi_bencana) . '"><br>
                    <label for="uraian">Jumlah KK:</label>
                    <input type="number" name="jumlah_kk" value="' . esc_attr($result->jumlah_kk) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Jiwa:</label>
                    <input type="number" name="jumlah_jiwa" value="' . esc_attr($result->jumlah_jiwa) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="tanggal">Jumlah Korban Laki-Laki:</label>
                    <input type="number" name="laki" value="' . esc_attr($result->laki) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Korban Perempuan:</label>
                    <input type="number" name="perempuan" value="' . esc_attr($result->perempuan) . '" inputmode="numeric" pattern="\d*"><br>
                    <input type="submit" name="submit" value="Update Data" class="update-button">
                    <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
                </form>';
        } else {
            // Jika data tidak ditemukan
            return '<p>Data tidak ditemukan.</p>';
        }
    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_data_bencana_alam', 'edit_data_bencana_alam');

function tambah_data_bencana_alam() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $ppkbd_database = 'websitekecamatanbintantimur';
    $table_name = $ppkbd_database . '.data_bencana_alam';

    if (isset($_POST['submit'])) {
        $tanggal = date('Y-m-d', strtotime($_POST['tanggal']));
                $jenis_bencana = sanitize_text_field($_POST['jenis_bencana']);
                $lokasi_bencana = sanitize_text_field($_POST['lokasi_bencana']);
                $jumlah_kk = intval($_POST['jumlah_kk']);
                $jumlah_jiwa = intval($_POST['jumlah_jiwa']);
                $laki = intval($_POST['laki']);
                $perempuan = intval($_POST['perempuan']);

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_bencana_alam',
            array(
                'nomor' => $new_nomor,
                'tanggal' => $tanggal,
                        'jenis_bencana' => $jenis_bencana,
                        'lokasi_bencana' => $lokasi_bencana,
                        'jumlah_kk' => $jumlah_kk,
                        'jumlah_jiwa' => $jumlah_jiwa,
                        'laki' => $laki,
                        'perempuan' => $perempuan,
            ),
            array('%d', '%s', '%s', '%s', '%d', '%d', '%d', '%d')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/data-potensi-dan-informasi/";
        </script>';
        exit;
    }

    return '
        <form method="post">
        <label for="uraian">Tanggal Kejadian:</label>
        <input type="date" name="tanggal" value="' . esc_attr($result->tanggal) .'"><br>
                    <label for="uraian">Jenis Bencana:</label>
                    <input type="text" name="jenis_bencana" value="' . esc_attr($result->jenis_bencana) . '"><br>
                    <label for="uraian">Lokasi Bencana:</label>
                    <input type="text" name="lokasi_bencana" value="' . esc_attr($result->lokasi_bencana) . '"><br>
                    <label for="uraian">Jumlah KK:</label>
                    <input type="number" name="jumlah_kk" value="' . esc_attr($result->jumlah_kk) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Jiwa:</label>
                    <input type="number" name="jumlah_jiwa" value="' . esc_attr($result->jumlah_jiwa) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="tanggal">Jumlah Korban Laki-Laki:</label>
                    <input type="number" name="laki" value="' . esc_attr($result->laki) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Jumlah Korban Perempuan:</label>
                    <input type="number" name="perempuan" value="' . esc_attr($result->perempuan) . '" inputmode="numeric" pattern="\d*"><br>
        <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_bencana_alam', 'tambah_data_bencana_alam');


function   data_usaha_kube_kijang_kota() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_usaha_kube_kijang_kota';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
            text-align: left;
        }
        .custom-table th{
            font-size: 10px;
        }    
        .custom-table td{
            font-size: 10px;
        }  
        .custom-table th, .custom-table td {
            padding: 12px;
        }
        .custom-table thead {
            background-color: #f2f2f2;
            color: #333;
        }
        .custom-table tr {
            border-bottom: 1px solid #ddd;
        }
        /* Media Query untuk layar kecil */
        @media (max-width: 768px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font */
            }
        }
        @media (max-width: 480px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font lebih kecil */
            }
        }
        </style>';

        $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
        $output .= '<table class="custom-table">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Kelurahan</th>';
        $output .= '<th style="padding:12px;">Nama Kube</th>';
        $output .= '<th style="padding:12px;">Nama Ketua Kube</th>';
        $output .= '<th style="padding:12px;">Alamat Kube</th>';
        $output .= '<th style="padding:12px;">No.HP</th>';
                            // Tambahkan kolom "Aksi" jika admin login
                            if (current_user_can('administrator')) {
                                $output .= '<th style="padding:12px;">Aksi</th>';
                            }
        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->kelurahan) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->nama_kube) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->nama_ketua_kube) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->alamat_kube) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor_hp) . '</td>';
              // Jika admin login, tampilkan tombol Edit
                        if (current_user_can('administrator')) {
                            $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-data-bidang-usaha-kelompok-bersama-kube-kijang-kota?id=' . $row->id . '">Edit</a></td>';
            
                        }
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }
    if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-data-bidang-usaha-kelompok-bersama-kube-kijang-kota" class="tambah-button">Tambah Data Bidang Usaha Kelompok Bersama (Kube) Kijang Kota</a>';
        $output .= '</div>';
    }

    return $output;
}

function edit_data_usaha_kube_kijang_kota() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $ppkbd_database = 'websitekecamatanbintantimur';
    $table_name = $ppkbd_database . '.data_usaha_kube_kijang_kota'; // ppkbd tabel yang benar

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = sanitize_text_field($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_usaha_kube_kijang_kota',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/data-potensi/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
            // Jika form disubmit untuk update
            if (isset($_POST['submit'])) {
                // Validasi dan sanitasi input dari form
                $kelurahan = sanitize_text_field($_POST['kelurahan']);
                $nama_kube = sanitize_text_field($_POST['nama_kube']);
                $nama_ketua_kube = sanitize_text_field($_POST['nama_ketua_kube']);
                $alamat_kube = sanitize_text_field($_POST['alamat_kube']);
                $nomor_hp = sanitize_text_field($_POST['nomor_hp']);

                // Update data ke database
                $wpdb->update(
                    'data_usaha_kube_kijang_kota',
                    array(
                        'kelurahan' => $kelurahan,
                        'nama_kube' => $nama_kube,
                        'nama_ketua_kube' => $nama_ketua_kube,
                        'alamat_kube' => $alamat_kube,
                        'nomor_hp' => $nomor_hp,
                    ),
                    array('id' => $id),  // Syarat untuk update berdasarkan ID
                    array('%s', '%s', '%s', '%s', '%s'),  // Format tipe data
                    array('%d')  // Format untuk ID
                );

                // Redirect setelah data berhasil diupdate
                echo '<script type="text/javascript">
                    alert("Data berhasil diupdate.");
                    window.location.href = "http://websitekecamatanbintantimur.test/data-potensi/";
                </script>';
                exit;
            }

            // Tampilkan form untuk mengedit data
            return '
                <form method="post">
                    <label for="uraian">Nama Kelurahan:</label>
                    <input type="text" name="kelurahan" value="' . esc_attr($result->kelurahan) . '"><br>
                    <label for="uraian">Nama KUBE:</label>
                    <input type="text" name="nama_kube" value="' . esc_attr($result->nama_kube) . '"><br>
                    <label for="uraian">Nama Ketua:</label>
                    <input type="text" name="nama_ketua_kube" value="' . esc_attr($result->nama_ketua_kube) . '"><br>
                    <label for="uraian">Alamat KUBE:</label>
                    <input type="text" name="alamat_kube" value="' . esc_attr($result->alamat_kube) . '"><br>
                    <label for="uraian">No.HP:</label>
                    <input type="text" name="nomor_hp" value="' . esc_attr($result->nomor_hp) . '"><br>
                    <input type="submit" name="submit" value="Update Data" class="update-button">
                    <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
                </form>';
        } else {
            // Jika data tidak ditemukan
            return '<p>Data tidak ditemukan.</p>';
        }
    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_data_usaha_kube_kijang_kota', 'edit_data_usaha_kube_kijang_kota');


function tambah_data_usaha_kube_kijang_kota() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $ppkbd_database = 'websitekecamatanbintantimur';
    $table_name = $ppkbd_database . '.data_usaha_kube_kijang_kota';

    if (isset($_POST['submit'])) {
        $kelurahan = sanitize_text_field($_POST['kelurahan']);
                $nama_kube = sanitize_text_field($_POST['nama_kube']);
                $nama_ketua_kube = sanitize_text_field($_POST['nama_ketua_kube']);
                $alamat_kube = sanitize_text_field($_POST['alamat_kube']);
                $nomor_hp = sanitize_text_field($_POST['nomor_hp']);

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_usaha_kube_kijang_kota',
            array(
                'nomor' => $new_nomor,
                'kelurahan' => $kelurahan,
                        'nama_kube' => $nama_kube,
                        'nama_ketua_kube' => $nama_ketua_kube,
                        'alamat_kube' => $alamat_kube,
                        'nomor_hp' => $nomor_hp,
            ),
            array('%d', '%s', '%s', '%s', '%s', '%s')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/data-potensi/";
        </script>';
        exit;
    }

    return '
        <form method="post">
        <label for="uraian">Nama Kelurahan:</label>
                    <input type="text" name="kelurahan" value="' . esc_attr($result->kelurahan) . '"><br>
                    <label for="uraian">Nama KUBE:</label>
                    <input type="text" name="nama_kube" value="' . esc_attr($result->nama_kube) . '"><br>
                    <label for="uraian">Nama Ketua:</label>
                    <input type="text" name="nama_ketua_kube" value="' . esc_attr($result->nama_ketua_kube) . '"><br>
                    <label for="uraian">Alamat KUBE:</label>
                    <input type="text" name="alamat_kube" value="' . esc_attr($result->alamat_kube) . '"><br>
                    <label for="uraian">No.HP:</label>
                    <input type="text" name="nomor_hp" value="' . esc_attr($result->nomor_hp) . '"><br>
        <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_usaha_kube_kijang_kota', 'tambah_data_usaha_kube_kijang_kota');


function   data_usaha_kube_sungai_enam_dan_gunung_lengkuas() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_usaha_kube_sungai_enam_dan_gunung_lengkuas';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
            text-align: left;
        }
        .custom-table th{
            font-size: 10px;
        }    
        .custom-table td{
            font-size: 10px;
        }  
        .custom-table th, .custom-table td {
            padding: 12px;
        }
        .custom-table thead {
            background-color: #f2f2f2;
            color: #333;
        }
        .custom-table tr {
            border-bottom: 1px solid #ddd;
        }
        /* Media Query untuk layar kecil */
        @media (max-width: 768px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font */
            }
        }
        @media (max-width: 480px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font lebih kecil */
            }
        }
        </style>';

        $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
        $output .= '<table class="custom-table">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Kelurahan</th>';
        $output .= '<th style="padding:12px;">Nama Kube</th>';
        $output .= '<th style="padding:12px;">Nama Ketua Kube</th>';
        $output .= '<th style="padding:12px;">Alamat Kube</th>';
        $output .= '<th style="padding:12px;">No.HP</th>';
                            // Tambahkan kolom "Aksi" jika admin login
                            if (current_user_can('administrator')) {
                                $output .= '<th style="padding:12px;">Aksi</th>';
                            }
        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->kelurahan) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->nama_kube) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->nama_ketua_kube) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->alamat_kube) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor_hp) . '</td>';
                                      // Jika admin login, tampilkan tombol Edit
                                      if (current_user_can('administrator')) {
                                        $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-data-bidang-usaha-kelompok-bersama-kube-sungai-enam-dan-gunung-lengkuas?id=' . $row->id . '">Edit</a></td>';
                        
                                    }
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }
    if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-data-bidang-usaha-kelompok-bersama-kube-sungai-enam-dan-gunung-lengkuas" class="tambah-button">Tambah Data Bidang Usaha Kelompok Bersama (Kube) Sungai Enam dan Gunung Lengkuas</a>';
        $output .= '</div>';
    }

    return $output;
}

function edit_data_usaha_kube_sungai_enam_dan_gunung_lengkuas() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $ppkbd_database = 'websitekecamatanbintantimur';
    $table_name = $ppkbd_database . '.data_usaha_kube_sungai_enam_dan_gunung_lengkuas'; // ppkbd tabel yang benar

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = sanitize_text_field($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_usaha_kube_sungai_enam_dan_gunung_lengkuas',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/data-potensi/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
            // Jika form disubmit untuk update
            if (isset($_POST['submit'])) {
                // Validasi dan sanitasi input dari form
                $kelurahan = sanitize_text_field($_POST['kelurahan']);
                $nama_kube = sanitize_text_field($_POST['nama_kube']);
                $nama_ketua_kube = sanitize_text_field($_POST['nama_ketua_kube']);
                $alamat_kube = sanitize_text_field($_POST['alamat_kube']);
                $nomor_hp = sanitize_text_field($_POST['nomor_hp']);

                // Update data ke database
                $wpdb->update(
                    'data_usaha_kube_sungai_enam_dan_gunung_lengkuas',
                    array(
                        'kelurahan' => $kelurahan,
                        'nama_kube' => $nama_kube,
                        'nama_ketua_kube' => $nama_ketua_kube,
                        'alamat_kube' => $alamat_kube,
                        'nomor_hp' => $nomor_hp,
                    ),
                    array('id' => $id),  // Syarat untuk update berdasarkan ID
                    array('%s', '%s', '%s', '%s', '%s'),  // Format tipe data
                    array('%d')  // Format untuk ID
                );

                // Redirect setelah data berhasil diupdate
                echo '<script type="text/javascript">
                    alert("Data berhasil diupdate.");
                    window.location.href = "http://websitekecamatanbintantimur.test/data-potensi/";
                </script>';
                exit;
            }

            // Tampilkan form untuk mengedit data
            return '
                <form method="post">
                    <label for="uraian">Nama Kelurahan:</label>
                    <input type="text" name="kelurahan" value="' . esc_attr($result->kelurahan) . '"><br>
                    <label for="uraian">Nama KUBE:</label>
                    <input type="text" name="nama_kube" value="' . esc_attr($result->nama_kube) . '"><br>
                    <label for="uraian">Nama Ketua:</label>
                    <input type="text" name="nama_ketua_kube" value="' . esc_attr($result->nama_ketua_kube) . '"><br>
                    <label for="uraian">Alamat KUBE:</label>
                    <input type="text" name="alamat_kube" value="' . esc_attr($result->alamat_kube) . '"><br>
                    <label for="uraian">No.HP:</label>
                    <input type="text" name="nomor_hp" value="' . esc_attr($result->nomor_hp) . '"><br>
                    <input type="submit" name="submit" value="Update Data" class="update-button">
                    <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
                </form>';
        } else {
            // Jika data tidak ditemukan
            return '<p>Data tidak ditemukan.</p>';
        }
    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_data_usaha_kube_sungai_enam_dan_gunung_lengkuas', 'edit_data_usaha_kube_sungai_enam_dan_gunung_lengkuas');


function tambah_data_usaha_kube_sungai_enam_dan_gunung_lengkuas() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $ppkbd_database = 'websitekecamatanbintantimur';
    $table_name = $ppkbd_database . '.data_usaha_kube_sungai_enam_dan_gunung_lengkuas';

    if (isset($_POST['submit'])) {
        $kelurahan = sanitize_text_field($_POST['kelurahan']);
                $nama_kube = sanitize_text_field($_POST['nama_kube']);
                $nama_ketua_kube = sanitize_text_field($_POST['nama_ketua_kube']);
                $alamat_kube = sanitize_text_field($_POST['alamat_kube']);
                $nomor_hp = sanitize_text_field($_POST['nomor_hp']);

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_usaha_kube_sungai_enam_dan_gunung_lengkuas',
            array(
                'nomor' => $new_nomor,
                'kelurahan' => $kelurahan,
                        'nama_kube' => $nama_kube,
                        'nama_ketua_kube' => $nama_ketua_kube,
                        'alamat_kube' => $alamat_kube,
                        'nomor_hp' => $nomor_hp,
            ),
            array('%d', '%s', '%s', '%s', '%s', '%s')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/data-potensi/";
        </script>';
        exit;
    }

    return '
        <form method="post">
        <label for="uraian">Nama Kelurahan:</label>
                    <input type="text" name="kelurahan" value="' . esc_attr($result->kelurahan) . '"><br>
                    <label for="uraian">Nama KUBE:</label>
                    <input type="text" name="nama_kube" value="' . esc_attr($result->nama_kube) . '"><br>
                    <label for="uraian">Nama Ketua:</label>
                    <input type="text" name="nama_ketua_kube" value="' . esc_attr($result->nama_ketua_kube) . '"><br>
                    <label for="uraian">Alamat KUBE:</label>
                    <input type="text" name="alamat_kube" value="' . esc_attr($result->alamat_kube) . '"><br>
                    <label for="uraian">No.HP:</label>
                    <input type="text" name="nomor_hp" value="' . esc_attr($result->nomor_hp) . '"><br>
        <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_usaha_kube_sungai_enam_dan_gunung_lengkuas', 'tambah_data_usaha_kube_sungai_enam_dan_gunung_lengkuas');


function   data_usaha_kube_sungai_lekop() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_usaha_kube_sungai_lekop';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
            text-align: left;
        }
        .custom-table th{
            font-size: 10px;
        }    
        .custom-table td{
            font-size: 10px;
        }  
        .custom-table th, .custom-table td {
            padding: 12px;
        }
        .custom-table thead {
            background-color: #f2f2f2;
            color: #333;
        }
        .custom-table tr {
            border-bottom: 1px solid #ddd;
        }
        /* Media Query untuk layar kecil */
        @media (max-width: 768px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font */
            }
        }
        @media (max-width: 480px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font lebih kecil */
            }
        }
        </style>';

        $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
        $output .= '<table class="custom-table">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Kelurahan</th>';
        $output .= '<th style="padding:12px;">Nama Kube</th>';
        $output .= '<th style="padding:12px;">Nama Ketua Kube</th>';
        $output .= '<th style="padding:12px;">Alamat Kube</th>';
        $output .= '<th style="padding:12px;">No.HP</th>';
                            // Tambahkan kolom "Aksi" jika admin login
                            if (current_user_can('administrator')) {
                                $output .= '<th style="padding:12px;">Aksi</th>';
                            }
        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->kelurahan) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->nama_kube) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->nama_ketua_kube) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->alamat_kube) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor_hp) . '</td>';
                          // Jika admin login, tampilkan tombol Edit
                          if (current_user_can('administrator')) {
                            $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-data-bidang-usaha-kelompok-bersama-kube-sungai-lekop?id=' . $row->id . '">Edit</a></td>';
            
                        }
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }
    if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-data-bidang-usaha-kelompok-bersama-kube-sungai-lekop" class="tambah-button">Tambah Data Bidang Usaha Kelompok Bersama (Kube) Sungai Lekop</a>';
        $output .= '</div>';
    }
    return $output;
}

function edit_data_usaha_kube_sungai_lekop() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $ppkbd_database = 'websitekecamatanbintantimur';
    $table_name = $ppkbd_database . '.data_usaha_kube_sungai_lekop'; // ppkbd tabel yang benar

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = sanitize_text_field($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_usaha_kube_sungai_lekop',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/data-potensi/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
            // Jika form disubmit untuk update
            if (isset($_POST['submit'])) {
                // Validasi dan sanitasi input dari form
                $kelurahan = sanitize_text_field($_POST['kelurahan']);
                $nama_kube = sanitize_text_field($_POST['nama_kube']);
                $nama_ketua_kube = sanitize_text_field($_POST['nama_ketua_kube']);
                $alamat_kube = sanitize_text_field($_POST['alamat_kube']);
                $nomor_hp = sanitize_text_field($_POST['nomor_hp']);

                // Update data ke database
                $wpdb->update(
                    'data_usaha_kube_sungai_lekop',
                    array(
                        'kelurahan' => $kelurahan,
                        'nama_kube' => $nama_kube,
                        'nama_ketua_kube' => $nama_ketua_kube,
                        'alamat_kube' => $alamat_kube,
                        'nomor_hp' => $nomor_hp,
                    ),
                    array('id' => $id),  // Syarat untuk update berdasarkan ID
                    array('%s', '%s', '%s', '%s', '%s'),  // Format tipe data
                    array('%d')  // Format untuk ID
                );

                // Redirect setelah data berhasil diupdate
                echo '<script type="text/javascript">
                    alert("Data berhasil diupdate.");
                    window.location.href = "http://websitekecamatanbintantimur.test/data-potensi/";
                </script>';
                exit;
            }

            // Tampilkan form untuk mengedit data
            return '
                <form method="post">
                    <label for="uraian">Nama Kelurahan:</label>
                    <input type="text" name="kelurahan" value="' . esc_attr($result->kelurahan) . '"><br>
                    <label for="uraian">Nama KUBE:</label>
                    <input type="text" name="nama_kube" value="' . esc_attr($result->nama_kube) . '"><br>
                    <label for="uraian">Nama Ketua:</label>
                    <input type="text" name="nama_ketua_kube" value="' . esc_attr($result->nama_ketua_kube) . '"><br>
                    <label for="uraian">Alamat KUBE:</label>
                    <input type="text" name="alamat_kube" value="' . esc_attr($result->alamat_kube) . '"><br>
                    <label for="uraian">No.HP:</label>
                    <input type="text" name="nomor_hp" value="' . esc_attr($result->nomor_hp) . '"><br>
                    <input type="submit" name="submit" value="Update Data" class="update-button">
                    <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
                </form>';
        } else {
            // Jika data tidak ditemukan
            return '<p>Data tidak ditemukan.</p>';
        }
    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_data_usaha_kube_sungai_lekop', 'edit_data_usaha_kube_sungai_lekop');


function tambah_data_usaha_kube_sungai_lekop() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $ppkbd_database = 'websitekecamatanbintantimur';
    $table_name = $ppkbd_database . '.data_usaha_kube_sungai_lekop';

    if (isset($_POST['submit'])) {
        $kelurahan = sanitize_text_field($_POST['kelurahan']);
                $nama_kube = sanitize_text_field($_POST['nama_kube']);
                $nama_ketua_kube = sanitize_text_field($_POST['nama_ketua_kube']);
                $alamat_kube = sanitize_text_field($_POST['alamat_kube']);
                $nomor_hp = sanitize_text_field($_POST['nomor_hp']);

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_usaha_kube_sungai_lekop',
            array(
                'nomor' => $new_nomor,
                'kelurahan' => $kelurahan,
                        'nama_kube' => $nama_kube,
                        'nama_ketua_kube' => $nama_ketua_kube,
                        'alamat_kube' => $alamat_kube,
                        'nomor_hp' => $nomor_hp,
            ),
            array('%d', '%s', '%s', '%s', '%s', '%s')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/data-potensi/";
        </script>';
        exit;
    }

    return '
        <form method="post">
        <label for="uraian">Nama Kelurahan:</label>
                    <input type="text" name="kelurahan" value="' . esc_attr($result->kelurahan) . '"><br>
                    <label for="uraian">Nama KUBE:</label>
                    <input type="text" name="nama_kube" value="' . esc_attr($result->nama_kube) . '"><br>
                    <label for="uraian">Nama Ketua:</label>
                    <input type="text" name="nama_ketua_kube" value="' . esc_attr($result->nama_ketua_kube) . '"><br>
                    <label for="uraian">Alamat KUBE:</label>
                    <input type="text" name="alamat_kube" value="' . esc_attr($result->alamat_kube) . '"><br>
                    <label for="uraian">No.HP:</label>
                    <input type="text" name="nomor_hp" value="' . esc_attr($result->nomor_hp) . '"><br>
        <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_usaha_kube_sungai_lekop', 'tambah_data_usaha_kube_sungai_lekop');


function   data_ikm() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_ikm';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
            text-align: left;
        }
        .custom-table th{
            font-size: 10px;
        }    
        .custom-table td{
            font-size: 10px;
        }  
        .custom-table th, .custom-table td {
            padding: 12px;
        }
        .custom-table thead {
            background-color: #f2f2f2;
            color: #333;
        }
        .custom-table tr {
            border-bottom: 1px solid #ddd;
        }
        /* Media Query untuk layar kecil */
        @media (max-width: 768px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font */
            }
        }
        @media (max-width: 480px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font lebih kecil */
            }
        }
        </style>';

        $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
        $output .= '<table class="custom-table">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Nama Pemilik</th>';
        $output .= '<th style="padding:12px;">Alamat</th>';
        $output .= '<th style="padding:12px;">Nama Produk</th>';
                            // Tambahkan kolom "Aksi" jika admin login
                            if (current_user_can('administrator')) {
                                $output .= '<th style="padding:12px;">Aksi</th>';
                            }
        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->nama_pemilik) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->alamat) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->nama_produk) . '</td>';
                                    // Jika admin login, tampilkan tombol Edit
                                    if (current_user_can('administrator')) {
                                        $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-data-industri-kecil-dan-menengah-ikm?id=' . $row->id . '">Edit</a></td>';
                        
                                    }
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }
    if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-data-industri-kecil-dan-menengah-ikm" class="tambah-button">Tambah Data Industri Kecil Dan Menengah (IKM)</a>';
        $output .= '</div>';
    }
    return $output;
}

function edit_data_ikm() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $ppkbd_database = 'websitekecamatanbintantimur';
    $table_name = $ppkbd_database . '.data_ikm'; // ppkbd tabel yang benar

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = sanitize_text_field($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_ikm',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/data-potensi/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
            // Jika form disubmit untuk update
            if (isset($_POST['submit'])) {
                // Validasi dan sanitasi input dari form
                $nama_pemilik = sanitize_text_field($_POST['nama_pemilik']);
                $alamat = sanitize_text_field($_POST['alamat']);
                $nama_produk = sanitize_text_field($_POST['nama_produk']);

                // Update data ke database
                $wpdb->update(
                    'data_ikm',
                    array(
                        'nama_pemilik' => $nama_pemilik,
                        'alamat' => $alamat,
                        'nama_produk' => $nama_produk,
                    ),
                    array('id' => $id),  // Syarat untuk update berdasarkan ID
                    array('%s', '%s', '%s'),  // Format tipe data
                    array('%d')  // Format untuk ID
                );

                // Redirect setelah data berhasil diupdate
                echo '<script type="text/javascript">
                    alert("Data berhasil diupdate.");
                    window.location.href = "http://websitekecamatanbintantimur.test/data-potensi/";
                </script>';
                exit;
            }

            // Tampilkan form untuk mengedit data
            return '
                <form method="post">
                    <label for="uraian">Nama Pemilik:</label>
                    <input type="text" name="nama_pemilik" value="' . esc_attr($result->nama_pemilik) . '"><br>
                    <label for="uraian">Alamat:</label>
                    <input type="text" name="alamat" value="' . esc_attr($result->alamat) . '"><br>
                    <label for="uraian">Nama Produk:</label>
                    <input type="text" name="nama_produk" value="' . esc_attr($result->nama_produk) . '"><br>
                    <input type="submit" name="submit" value="Update Data" class="update-button">
                    <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
                </form>';
        } else {
            // Jika data tidak ditemukan
            return '<p>Data tidak ditemukan.</p>';
        }
    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_data_ikm', 'edit_data_ikm');


function tambah_data_ikm() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $ppkbd_database = 'websitekecamatanbintantimur';
    $table_name = $ppkbd_database . '.data_ikm';

    if (isset($_POST['submit'])) {
        $nama_pemilik = sanitize_text_field($_POST['nama_pemilik']);
                $alamat = sanitize_text_field($_POST['alamat']);
                $nama_produk = sanitize_text_field($_POST['nama_produk']);

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_ikm',
            array(
                'nomor' => $new_nomor,
                'nama_pemilik' => $nama_pemilik,
                        'alamat' => $alamat,
                        'nama_produk' => $nama_produk,
            ),
            array('%d', '%s', '%s', '%s')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/data-potensi/";
        </script>';
        exit;
    }

    return '
        <form method="post">
        <label for="uraian">Nama Pemilik:</label>
                    <input type="text" name="nama_pemilik" value="' . esc_attr($result->nama_pemilik) . '"><br>
                    <label for="uraian">Alamat:</label>
                    <input type="text" name="alamat" value="' . esc_attr($result->alamat) . '"><br>
                    <label for="uraian">Nama Produk:</label>
                    <input type="text" name="nama_produk" value="' . esc_attr($result->nama_produk) . '"><br>
        <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_ikm', 'tambah_data_ikm');


function   data_kelompok_tani_kelurahan_kijang_kota() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_kelompok_tani_kelurahan_kijang_kota';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
            text-align: left;
        }
        .custom-table th{
            font-size: 10px;
        }    
        .custom-table td{
            font-size: 10px;
        }  
        .custom-table th, .custom-table td {
            padding: 12px;
        }
        .custom-table thead {
            background-color: #f2f2f2;
            color: #333;
        }
        .custom-table tr {
            border-bottom: 1px solid #ddd;
        }
        /* Media Query untuk layar kecil */
        @media (max-width: 768px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font */
            }
        }
        @media (max-width: 480px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font lebih kecil */
            }
        }
        </style>';

        $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
        $output .= '<table class="custom-table">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Nama Kelompok Tani</th>';
        $output .= '<th style="padding:12px;">Nama Ketua</th>';
        $output .= '<th style="padding:12px;">Alamat Sekretariat</th>';
        $output .= '<th style="padding:12px;">Tahun Bentuk</th>';
                                    // Tambahkan kolom "Aksi" jika admin login
                                    if (current_user_can('administrator')) {
                                        $output .= '<th style="padding:12px;">Aksi</th>';
                                    }
        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->nama_kelompok_tani) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->nama_ketua) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->alamat_sekretariat) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->tahun_dibentuk) . '</td>';
                                                // Jika admin login, tampilkan tombol Edit
                                                if (current_user_can('administrator')) {
                                                    $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-data-kelompok-tani-kelurahan-kijang-kota?id=' . $row->id . '">Edit</a></td>';
                                    
                                                }
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }
    if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-data-kelompok-tani-kelurahan-kijang-kota" class="tambah-button">Tambah Data Kelompok Tani Kelurahan Kijang Kota</a>';
        $output .= '</div>';
    }

    return $output;
}

function edit_data_kelompok_tani_kelurahan_kijang_kota() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $ppkbd_database = 'websitekecamatanbintantimur';
    $table_name = $ppkbd_database . '.data_kelompok_tani_kelurahan_kijang_kota'; // ppkbd tabel yang benar

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = sanitize_text_field($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_kelompok_tani_kelurahan_kijang_kota',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/data-potensi/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
            // Jika form disubmit untuk update
            if (isset($_POST['submit'])) {
                // Validasi dan sanitasi input dari form
                $nama_kelompok_tani = sanitize_text_field($_POST['nama_kelompok_tani']);
                $nama_ketua = sanitize_text_field($_POST['nama_ketua']);
                $alamat_sekretariat = sanitize_text_field($_POST['alamat_sekretariat']);
                $tahun_dibentuk = sanitize_text_field($_POST['tahun_dibentuk']);

                // Update data ke database
                $wpdb->update(
                    'data_kelompok_tani_kelurahan_kijang_kota',
                    array(
                        'nama_kelompok_tani' => $nama_kelompok_tani,
                        'nama_ketua' => $nama_ketua,
                        'alamat_sekretariat' => $alamat_sekretariat,
                        'tahun_dibentuk' => $tahun_dibentuk,
                    ),
                    array('id' => $id),  // Syarat untuk update berdasarkan ID
                    array('%s', '%s', '%s', '%s'),  // Format tipe data
                    array('%d')  // Format untuk ID
                );

                // Redirect setelah data berhasil diupdate
                echo '<script type="text/javascript">
                    alert("Data berhasil diupdate.");
                    window.location.href = "http://websitekecamatanbintantimur.test/data-potensi/";
                </script>';
                exit;
            }

            // Tampilkan form untuk mengedit data
            return '
                <form method="post">
                    <label for="uraian">Nama Kelompok Tani:</label>
                    <input type="text" name="nama_kelompok_tani" value="' . esc_attr($result->nama_kelompok_tani) . '"><br>
                    <label for="uraian">Nama Ketua:</label>
                    <input type="text" name="nama_ketua" value="' . esc_attr($result->nama_ketua) . '"><br>
                    <label for="uraian">Alamat Sekretariat:</label>
                    <input type="text" name="alamat_sekretariat" value="' . esc_attr($result->alamat_sekretariat) . '"><br>
                    <label for="uraian">Tahun Dibentuk:</label>
                    <input type="text" name="tahun_dibentuk" value="' . esc_attr($result->tahun_dibentuk) . '"><br>
                    <input type="submit" name="submit" value="Update Data" class="update-button">
                    <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
                </form>';
        } else {
            // Jika data tidak ditemukan
            return '<p>Data tidak ditemukan.</p>';
        }
    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_data_kelompok_tani_kelurahan_kijang_kota', 'edit_data_kelompok_tani_kelurahan_kijang_kota');


function tambah_data_kelompok_tani_kelurahan_kijang_kota() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $ppkbd_database = 'websitekecamatanbintantimur';
    $table_name = $ppkbd_database . '.data_kelompok_tani_kelurahan_kijang_kota';

    if (isset($_POST['submit'])) {
        $nama_kelompok_tani = sanitize_text_field($_POST['nama_kelompok_tani']);
        $nama_ketua = sanitize_text_field($_POST['nama_ketua']);
        $alamat_sekretariat = sanitize_text_field($_POST['alamat_sekretariat']);
        $tahun_dibentuk = sanitize_text_field($_POST['tahun_dibentuk']);

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_kelompok_tani_kelurahan_kijang_kota',
            array(
                'nomor' => $new_nomor,
                'nama_kelompok_tani' => $nama_kelompok_tani,
                        'nama_ketua' => $nama_ketua,
                        'alamat_sekretariat' => $alamat_sekretariat,
                        'tahun_dibentuk' => $tahun_dibentuk,
            ),
            array('%d', '%s', '%s', '%s', '%s')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/data-potensi/";
        </script>';
        exit;
    }

    return '
        <form method="post">
        <label for="uraian">Nama Kelompok Tani:</label>
                    <input type="text" name="nama_kelompok_tani" value="' . esc_attr($result->nama_kelompok_tani) . '"><br>
                    <label for="uraian">Nama Ketua:</label>
                    <input type="text" name="nama_ketua" value="' . esc_attr($result->nama_ketua) . '"><br>
                    <label for="uraian">Alamat Sekretariat:</label>
                    <input type="text" name="alamat_sekretariat" value="' . esc_attr($result->alamat_sekretariat) . '"><br>
                    <label for="uraian">Tahun Dibentuk:</label>
                    <input type="text" name="tahun_dibentuk" value="' . esc_attr($result->tahun_dibentuk) . '"><br>
        <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_kelompok_tani_kelurahan_kijang_kota', 'tambah_data_kelompok_tani_kelurahan_kijang_kota');


function   data_kelompok_tani_kelurahan_sungai_enam() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_kelompok_tani_kelurahan_sungai_enam';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
            text-align: left;
        }
        .custom-table th{
            font-size: 10px;
        }    
        .custom-table td{
            font-size: 10px;
        }  
        .custom-table th, .custom-table td {
            padding: 12px;
        }
        .custom-table thead {
            background-color: #f2f2f2;
            color: #333;
        }
        .custom-table tr {
            border-bottom: 1px solid #ddd;
        }
        /* Media Query untuk layar kecil */
        @media (max-width: 768px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font */
            }
        }
        @media (max-width: 480px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font lebih kecil */
            }
        }
        </style>';

        $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
        $output .= '<table class="custom-table">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Nama Kelompok Tani</th>';
        $output .= '<th style="padding:12px;">Nama Ketua</th>';
        $output .= '<th style="padding:12px;">Alamat Sekretariat</th>';
        $output .= '<th style="padding:12px;">Tahun Bentuk</th>';
                                    // Tambahkan kolom "Aksi" jika admin login
                                    if (current_user_can('administrator')) {
                                        $output .= '<th style="padding:12px;">Aksi</th>';
                                    }
        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->nama_kelompok_tani) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->nama_ketua) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->alamat_sekretariat) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->tahun_dibentuk) . '</td>';
                                                // Jika admin login, tampilkan tombol Edit
                                                if (current_user_can('administrator')) {
                                                    $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-data-kelompok-tani-kelurahan-sungai-enam?id=' . $row->id . '">Edit</a></td>';
                                    
                                                }
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }
    if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-data-kelompok-tani-kelurahan-sungai-enam" class="tambah-button">Tambah Data Kelompok Tani Kelurahan Sungai Enam</a>';
        $output .= '</div>';
    }


    return $output;
}

function edit_data_kelompok_tani_kelurahan_sungai_enam() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $ppkbd_database = 'websitekecamatanbintantimur';
    $table_name = $ppkbd_database . '.data_kelompok_tani_kelurahan_sungai_enam'; // ppkbd tabel yang benar

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = sanitize_text_field($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_kelompok_tani_kelurahan_sungai_enam',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/data-potensi/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
            // Jika form disubmit untuk update
            if (isset($_POST['submit'])) {
                // Validasi dan sanitasi input dari form
                $nama_kelompok_tani = sanitize_text_field($_POST['nama_kelompok_tani']);
                $nama_ketua = sanitize_text_field($_POST['nama_ketua']);
                $alamat_sekretariat = sanitize_text_field($_POST['alamat_sekretariat']);
                $tahun_dibentuk = sanitize_text_field($_POST['tahun_dibentuk']);

                // Update data ke database
                $wpdb->update(
                    'data_kelompok_tani_kelurahan_sungai_enam',
                    array(
                        'nama_kelompok_tani' => $nama_kelompok_tani,
                        'nama_ketua' => $nama_ketua,
                        'alamat_sekretariat' => $alamat_sekretariat,
                        'tahun_dibentuk' => $tahun_dibentuk,
                    ),
                    array('id' => $id),  // Syarat untuk update berdasarkan ID
                    array('%s', '%s', '%s', '%s'),  // Format tipe data
                    array('%d')  // Format untuk ID
                );

                // Redirect setelah data berhasil diupdate
                echo '<script type="text/javascript">
                    alert("Data berhasil diupdate.");
                    window.location.href = "http://websitekecamatanbintantimur.test/data-potensi/";
                </script>';
                exit;
            }

            // Tampilkan form untuk mengedit data
            return '
                <form method="post">
                    <label for="uraian">Nama Kelompok Tani:</label>
                    <input type="text" name="nama_kelompok_tani" value="' . esc_attr($result->nama_kelompok_tani) . '"><br>
                    <label for="uraian">Nama Ketua:</label>
                    <input type="text" name="nama_ketua" value="' . esc_attr($result->nama_ketua) . '"><br>
                    <label for="uraian">Alamat Sekretariat:</label>
                    <input type="text" name="alamat_sekretariat" value="' . esc_attr($result->alamat_sekretariat) . '"><br>
                    <label for="uraian">Tahun Dibentuk:</label>
                    <input type="text" name="tahun_dibentuk" value="' . esc_attr($result->tahun_dibentuk) . '"><br>
                    <input type="submit" name="submit" value="Update Data" class="update-button">
                    <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
                </form>';
        } else {
            // Jika data tidak ditemukan
            return '<p>Data tidak ditemukan.</p>';
        }
    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_data_kelompok_tani_kelurahan_sungai_enam', 'edit_data_kelompok_tani_kelurahan_sungai_enam');


function tambah_data_kelompok_tani_kelurahan_sungai_enam() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $ppkbd_database = 'websitekecamatanbintantimur';
    $table_name = $ppkbd_database . '.data_kelompok_tani_kelurahan_sungai_enam';

    if (isset($_POST['submit'])) {
        $nama_kelompok_tani = sanitize_text_field($_POST['nama_kelompok_tani']);
                $nama_ketua = sanitize_text_field($_POST['nama_ketua']);
                $alamat_sekretariat = sanitize_text_field($_POST['alamat_sekretariat']);
                $tahun_dibentuk = sanitize_text_field($_POST['tahun_dibentuk']);

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_kelompok_tani_kelurahan_sungai_enam',
            array(
                'nomor' => $new_nomor,
                'nama_kelompok_tani' => $nama_kelompok_tani,
                        'nama_ketua' => $nama_ketua,
                        'alamat_sekretariat' => $alamat_sekretariat,
                        'tahun_dibentuk' => $tahun_dibentuk,
            ),
            array('%d', '%s', '%s', '%s', '%s')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/data-potensi/";
        </script>';
        exit;
    }

    return '
        <form method="post">
        <label for="uraian">Nama Kelompok Tani:</label>
                    <input type="text" name="nama_kelompok_tani" value="' . esc_attr($result->nama_kelompok_tani) . '"><br>
                    <label for="uraian">Nama Ketua:</label>
                    <input type="text" name="nama_ketua" value="' . esc_attr($result->nama_ketua) . '"><br>
                    <label for="uraian">Alamat Sekretariat:</label>
                    <input type="text" name="alamat_sekretariat" value="' . esc_attr($result->alamat_sekretariat) . '"><br>
                    <label for="uraian">Tahun Dibentuk:</label>
                    <input type="text" name="tahun_dibentuk" value="' . esc_attr($result->tahun_dibentuk) . '"><br>
        <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_kelompok_tani_kelurahan_sungai_enam', 'tambah_data_kelompok_tani_kelurahan_sungai_enam');


function   data_kelompok_tani_kelurahan_gunung_lengkuas() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_kelompok_tani_kelurahan_gunung_lengkuas';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
            text-align: left;
        }
        .custom-table th{
            font-size: 10px;
        }    
        .custom-table td{
            font-size: 10px;
        }  
        .custom-table th, .custom-table td {
            padding: 12px;
        }
        .custom-table thead {
            background-color: #f2f2f2;
            color: #333;
        }
        .custom-table tr {
            border-bottom: 1px solid #ddd;
        }
        /* Media Query untuk layar kecil */
        @media (max-width: 768px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font */
            }
        }
        @media (max-width: 480px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font lebih kecil */
            }
        }
        </style>';

        $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
        $output .= '<table class="custom-table">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Nama Kelompok Tani</th>';
        $output .= '<th style="padding:12px;">Nama Ketua</th>';
        $output .= '<th style="padding:12px;">Alamat Sekretariat</th>';
        $output .= '<th style="padding:12px;">Tahun Bentuk</th>';
                                    // Tambahkan kolom "Aksi" jika admin login
                                    if (current_user_can('administrator')) {
                                        $output .= '<th style="padding:12px;">Aksi</th>';
                                    }
        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->nama_kelompok_tani) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->nama_ketua) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->alamat_sekretariat) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->tahun_dibentuk) . '</td>';
                                                // Jika admin login, tampilkan tombol Edit
                                                if (current_user_can('administrator')) {
                                                    $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-data-kelompok-tani-kelurahan-gunung-lengkuas?id=' . $row->id . '">Edit</a></td>';
                                    
                                                }
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }

    if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-data-kelompok-tani-kelurahan-gunung-lengkuas" class="tambah-button">Tambah Data Kelompok Tani Kelurahan Gunung Lengkuas</a>';
        $output .= '</div>';
    }

    return $output;
}

function edit_data_kelompok_tani_kelurahan_gunung_lengkuas() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $ppkbd_database = 'websitekecamatanbintantimur';
    $table_name = $ppkbd_database . '.data_kelompok_tani_kelurahan_gunung_lengkuas'; // ppkbd tabel yang benar

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = sanitize_text_field($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_kelompok_tani_kelurahan_gunung_lengkuas',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/data-potensi/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
            // Jika form disubmit untuk update
            if (isset($_POST['submit'])) {
                // Validasi dan sanitasi input dari form
                $nama_kelompok_tani = sanitize_text_field($_POST['nama_kelompok_tani']);
                $nama_ketua = sanitize_text_field($_POST['nama_ketua']);
                $alamat_sekretariat = sanitize_text_field($_POST['alamat_sekretariat']);
                $tahun_dibentuk = sanitize_text_field($_POST['tahun_dibentuk']);

                // Update data ke database
                $wpdb->update(
                    'data_kelompok_tani_kelurahan_gunung_lengkuas',
                    array(
                        'nama_kelompok_tani' => $nama_kelompok_tani,
                        'nama_ketua' => $nama_ketua,
                        'alamat_sekretariat' => $alamat_sekretariat,
                        'tahun_dibentuk' => $tahun_dibentuk,
                    ),
                    array('id' => $id),  // Syarat untuk update berdasarkan ID
                    array('%s', '%s', '%s', '%s'),  // Format tipe data
                    array('%d')  // Format untuk ID
                );

                // Redirect setelah data berhasil diupdate
                echo '<script type="text/javascript">
                    alert("Data berhasil diupdate.");
                    window.location.href = "http://websitekecamatanbintantimur.test/data-potensi/";
                </script>';
                exit;
            }

            // Tampilkan form untuk mengedit data
            return '
                <form method="post">
                    <label for="uraian">Nama Kelompok Tani:</label>
                    <input type="text" name="nama_kelompok_tani" value="' . esc_attr($result->nama_kelompok_tani) . '"><br>
                    <label for="uraian">Nama Ketua:</label>
                    <input type="text" name="nama_ketua" value="' . esc_attr($result->nama_ketua) . '"><br>
                    <label for="uraian">Alamat Sekretariat:</label>
                    <input type="text" name="alamat_sekretariat" value="' . esc_attr($result->alamat_sekretariat) . '"><br>
                    <label for="uraian">Tahun Dibentuk:</label>
                    <input type="text" name="tahun_dibentuk" value="' . esc_attr($result->tahun_dibentuk) . '"><br>
                    <input type="submit" name="submit" value="Update Data" class="update-button">
                    <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
                </form>';
        } else {
            // Jika data tidak ditemukan
            return '<p>Data tidak ditemukan.</p>';
        }
    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_data_kelompok_tani_kelurahan_gunung_lengkuas', 'edit_data_kelompok_tani_kelurahan_gunung_lengkuas');


function tambah_data_kelompok_tani_kelurahan_gunung_lengkuas() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $ppkbd_database = 'websitekecamatanbintantimur';
    $table_name = $ppkbd_database . '.data_kelompok_tani_kelurahan_gunung_lengkuas';

    if (isset($_POST['submit'])) {
        $nama_kelompok_tani = sanitize_text_field($_POST['nama_kelompok_tani']);
                $nama_ketua = sanitize_text_field($_POST['nama_ketua']);
                $alamat_sekretariat = sanitize_text_field($_POST['alamat_sekretariat']);
                $tahun_dibentuk = sanitize_text_field($_POST['tahun_dibentuk']);

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_kelompok_tani_kelurahan_gunung_lengkuas',
            array(
                'nomor' => $new_nomor,
                'nama_kelompok_tani' => $nama_kelompok_tani,
                        'nama_ketua' => $nama_ketua,
                        'alamat_sekretariat' => $alamat_sekretariat,
                        'tahun_dibentuk' => $tahun_dibentuk,
            ),
            array('%d', '%s', '%s', '%s', '%s')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/data-potensi/";
        </script>';
        exit;
    }

    return '
        <form method="post">
        <label for="uraian">Nama Kelompok Tani:</label>
                    <input type="text" name="nama_kelompok_tani" value="' . esc_attr($result->nama_kelompok_tani) . '"><br>
                    <label for="uraian">Nama Ketua:</label>
                    <input type="text" name="nama_ketua" value="' . esc_attr($result->nama_ketua) . '"><br>
                    <label for="uraian">Alamat Sekretariat:</label>
                    <input type="text" name="alamat_sekretariat" value="' . esc_attr($result->alamat_sekretariat) . '"><br>
                    <label for="uraian">Tahun Dibentuk:</label>
                    <input type="text" name="tahun_dibentuk" value="' . esc_attr($result->tahun_dibentuk) . '"><br>
        <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_kelompok_tani_kelurahan_gunung_lengkuas', 'tambah_data_kelompok_tani_kelurahan_gunung_lengkuas');

function   data_kelompok_tani_kelurahan_sungai_lekop() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_kelompok_tani_kelurahan_sungai_lekop';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
            text-align: left;
        }
        .custom-table th{
            font-size: 10px;
        }    
        .custom-table td{
            font-size: 10px;
        }  
        .custom-table th, .custom-table td {
            padding: 12px;
        }
        .custom-table thead {
            background-color: #f2f2f2;
            color: #333;
        }
        .custom-table tr {
            border-bottom: 1px solid #ddd;
        }
        /* Media Query untuk layar kecil */
        @media (max-width: 768px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font */
            }
        }
        @media (max-width: 480px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font lebih kecil */
            }
        }
        </style>';

        $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
        $output .= '<table class="custom-table">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Nama Kelompok Tani</th>';
        $output .= '<th style="padding:12px;">Nama Ketua</th>';
        $output .= '<th style="padding:12px;">Alamat Sekretariat</th>';
        $output .= '<th style="padding:12px;">Tahun Bentuk</th>';
                                    // Tambahkan kolom "Aksi" jika admin login
                                    if (current_user_can('administrator')) {
                                        $output .= '<th style="padding:12px;">Aksi</th>';
                                    }
        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->nama_kelompok_tani) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->nama_ketua) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->alamat_sekretariat) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->tahun_dibentuk) . '</td>';
                                    // Jika admin login, tampilkan tombol Edit
                        if (current_user_can('administrator')) {
                            $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-data-kelompok-tani-kelurahan-sungai-lekop?id=' . $row->id . '">Edit</a></td>';
            
                        }
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }
    if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-data-kelompok-tani-kelurahan-sungai-lekop" class="tambah-button">Tambah Data Kelompok Tani Kelurahan Sungai Lekop</a>';
        $output .= '</div>';
    }
    return $output;
}

function edit_data_kelompok_tani_kelurahan_sungai_lekop() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $ppkbd_database = 'websitekecamatanbintantimur';
    $table_name = $ppkbd_database . '.data_kelompok_tani_kelurahan_sungai_lekop'; // ppkbd tabel yang benar

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = sanitize_text_field($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_kelompok_tani_kelurahan_sungai_lekop',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/data-potensi/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
            // Jika form disubmit untuk update
            if (isset($_POST['submit'])) {
                // Validasi dan sanitasi input dari form
                $nama_kelompok_tani = sanitize_text_field($_POST['nama_kelompok_tani']);
                $nama_ketua = sanitize_text_field($_POST['nama_ketua']);
                $alamat_sekretariat = sanitize_text_field($_POST['alamat_sekretariat']);
                $tahun_dibentuk = sanitize_text_field($_POST['tahun_dibentuk']);

                // Update data ke database
                $wpdb->update(
                    'data_kelompok_tani_kelurahan_sungai_lekop',
                    array(
                        'nama_kelompok_tani' => $nama_kelompok_tani,
                        'nama_ketua' => $nama_ketua,
                        'alamat_sekretariat' => $alamat_sekretariat,
                        'tahun_dibentuk' => $tahun_dibentuk,
                    ),
                    array('id' => $id),  // Syarat untuk update berdasarkan ID
                    array('%s', '%s', '%s', '%s'),  // Format tipe data
                    array('%d')  // Format untuk ID
                );

                // Redirect setelah data berhasil diupdate
                echo '<script type="text/javascript">
                    alert("Data berhasil diupdate.");
                    window.location.href = "http://websitekecamatanbintantimur.test/data-potensi/";
                </script>';
                exit;
            }

            // Tampilkan form untuk mengedit data
            return '
                <form method="post">
                    <label for="uraian">Nama Kelompok Tani:</label>
                    <input type="text" name="nama_kelompok_tani" value="' . esc_attr($result->nama_kelompok_tani) . '"><br>
                    <label for="uraian">Nama Ketua:</label>
                    <input type="text" name="nama_ketua" value="' . esc_attr($result->nama_ketua) . '"><br>
                    <label for="uraian">Alamat Sekretariat:</label>
                    <input type="text" name="alamat_sekretariat" value="' . esc_attr($result->alamat_sekretariat) . '"><br>
                    <label for="uraian">Tahun Dibentuk:</label>
                    <input type="text" name="tahun_dibentuk" value="' . esc_attr($result->tahun_dibentuk) . '"><br>
                    <input type="submit" name="submit" value="Update Data" class="update-button">
                    <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
                </form>';
        } else {
            // Jika data tidak ditemukan
            return '<p>Data tidak ditemukan.</p>';
        }
    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_data_kelompok_tani_kelurahan_sungai_lekop', 'edit_data_kelompok_tani_kelurahan_sungai_lekop');


function tambah_data_kelompok_tani_kelurahan_sungai_lekop() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $ppkbd_database = 'websitekecamatanbintantimur';
    $table_name = $ppkbd_database . '.data_kelompok_tani_kelurahan_sungai_lekop';

    if (isset($_POST['submit'])) {
        $nama_kelompok_tani = sanitize_text_field($_POST['nama_kelompok_tani']);
                $nama_ketua = sanitize_text_field($_POST['nama_ketua']);
                $alamat_sekretariat = sanitize_text_field($_POST['alamat_sekretariat']);
                $tahun_dibentuk = sanitize_text_field($_POST['tahun_dibentuk']);

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_kelompok_tani_kelurahan_sungai_lekop',
            array(
                'nomor' => $new_nomor,
                'nama_kelompok_tani' => $nama_kelompok_tani,
                        'nama_ketua' => $nama_ketua,
                        'alamat_sekretariat' => $alamat_sekretariat,
                        'tahun_dibentuk' => $tahun_dibentuk,
            ),
            array('%d', '%s', '%s', '%s', '%s')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/data-potensi/";
        </script>';
        exit;
    }

    return '
        <form method="post">
        <label for="uraian">Nama Kelompok Tani:</label>
                    <input type="text" name="nama_kelompok_tani" value="' . esc_attr($result->nama_kelompok_tani) . '"><br>
                    <label for="uraian">Nama Ketua:</label>
                    <input type="text" name="nama_ketua" value="' . esc_attr($result->nama_ketua) . '"><br>
                    <label for="uraian">Alamat Sekretariat:</label>
                    <input type="text" name="alamat_sekretariat" value="' . esc_attr($result->alamat_sekretariat) . '"><br>
                    <label for="uraian">Tahun Dibentuk:</label>
                    <input type="text" name="tahun_dibentuk" value="' . esc_attr($result->tahun_dibentuk) . '"><br>
        <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_kelompok_tani_kelurahan_sungai_lekop', 'tambah_data_kelompok_tani_kelurahan_sungai_lekop');


function   data_luas_lahan_pertanian_dan_perkebunan() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_luas_lahan_pertanian_dan_perkebunan';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
            text-align: left;
        }
        .custom-table th{
            font-size: 10px;
        }    
        .custom-table td{
            font-size: 10px;
        }  
        .custom-table th, .custom-table td {
            padding: 12px;
        }
        .custom-table thead {
            background-color: #f2f2f2;
            color: #333;
        }
        .custom-table tr {
            border-bottom: 1px solid #ddd;
        }
        /* Media Query untuk layar kecil */
        @media (max-width: 768px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font */
            }
        }
        @media (max-width: 480px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font lebih kecil */
            }
        }
        </style>';

        $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
        $output .= '<table class="custom-table">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Komiditi</th>';
        $output .= '<th style="padding:12px;">Luas Lahan (Ha)</th>';
        $output .= '<th style="padding:12px;">Ket</th>';
                                    // Tambahkan kolom "Aksi" jika admin login
                                    if (current_user_can('administrator')) {
                                        $output .= '<th style="padding:12px;">Aksi</th>';
                                    }
        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->komiditi) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->luas_lahan) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->ket) . '</td>';
                                    // Jika admin login, tampilkan tombol Edit
                                    if (current_user_can('administrator')) {
                                        $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-data-luas-lahan-pertanian-dan-perkebunan?id=' . $row->id . '">Edit</a></td>';
                        
                                    }
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }
    if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-data-luas-lahan-pertanian-dan-perkebunan" class="tambah-button">Tambah Data Luas Lahan Pertanian Dan Perkebunan</a>';
        $output .= '</div>';
    }
    return $output;
}

function edit_data_luas_lahan_pertanian_dan_perkebunan() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $ppkbd_database = 'websitekecamatanbintantimur';
    $table_name = $ppkbd_database . '.data_luas_lahan_pertanian_dan_perkebunan'; // ppkbd tabel yang benar

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = sanitize_text_field($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_luas_lahan_pertanian_dan_perkebunan',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/data-potensi/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
            // Jika form disubmit untuk update
            if (isset($_POST['submit'])) {
                // Validasi dan sanitasi input dari form
                $komiditi = sanitize_text_field($_POST['komiditi']);
                $luas_lahan = floatval($_POST['luas_lahan']);
                $ket = sanitize_text_field($_POST['ket']);

                // Update data ke database
                $wpdb->update(
                    'data_luas_lahan_pertanian_dan_perkebunan',
                    array(
                        'komiditi' => $komiditi,
                        'luas_lahan' => $luas_lahan,
                        'ket' => $ket,
                    ),
                    array('id' => $id),  // Syarat untuk update berdasarkan ID
                    array('%s', '%.2f', '%s'),  // Format tipe data
                    array('%d')  // Format untuk ID
                );

                // Redirect setelah data berhasil diupdate
                echo '<script type="text/javascript">
                    alert("Data berhasil diupdate.");
                    window.location.href = "http://websitekecamatanbintantimur.test/data-potensi/";
                </script>';
                exit;
            }

            // Tampilkan form untuk mengedit data
            return '
                <form method="post">
                    <label for="uraian">Nama Komiditi:</label>
                    <input type="text" name="komiditi" value="' . esc_attr($result->komiditi) . '"><br>
                    <label for="uraian">Luas Lahan:</label>
                    <input type="number" name="luas_lahan" value="' . esc_attr($result->luas_lahan) . '" step="0.01" inputmode="decimal" pattern="[0-9]+(\.[0-9]{1,2})?" /><br>
                    <label for="uraian">Keterangan:</label>
                    <input type="text" name="ket" value="' . esc_attr($result->ket) . '"><br>
                    <input type="submit" name="submit" value="Update Data" class="update-button">
                    <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
                </form>';
        } else {
            // Jika data tidak ditemukan
            return '<p>Data tidak ditemukan.</p>';
        }
    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_data_luas_lahan_pertanian_dan_perkebunan', 'edit_data_luas_lahan_pertanian_dan_perkebunan');


function tambah_data_luas_lahan_pertanian_dan_perkebunan() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $ppkbd_database = 'websitekecamatanbintantimur';
    $table_name = $ppkbd_database . '.data_luas_lahan_pertanian_dan_perkebunan';

    if (isset($_POST['submit'])) {
        $komiditi = sanitize_text_field($_POST['komiditi']);
                $luas_lahan = floatval($_POST['luas_lahan']);
                $ket = sanitize_text_field($_POST['ket']);

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_luas_lahan_pertanian_dan_perkebunan',
            array(
                'nomor' => $new_nomor,
                'komiditi' => $komiditi,
                        'luas_lahan' => $luas_lahan,
                        'ket' => $ket,
            ),
            array('%d', '%s', '%.2f', '%s')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/data-potensi/";
        </script>';
        exit;
    }

    return '
        <form method="post">
        <label for="uraian">Nama Komiditi:</label>
                    <input type="text" name="komiditi" value="' . esc_attr($result->komiditi) . '"><br>
                    <label for="uraian">Luas Lahan:</label>
                    <input type="number" name="luas_lahan" value="' . esc_attr($result->luas_lahan) . '" step="0.01" inputmode="decimal" pattern="[0-9]+(\.[0-9]{1,2})?" /><br>
                    <label for="uraian">Keterangan:</label>
                    <input type="text" name="ket" value="' . esc_attr($result->ket) . '"><br>
        <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_luas_lahan_pertanian_dan_perkebunan', 'tambah_data_luas_lahan_pertanian_dan_perkebunan');


function   data_tanaman_hortikultura() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_tanaman_hortikultura';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
            text-align: left;
        }
        .custom-table th{
            font-size: 10px;
        }    
        .custom-table td{
            font-size: 10px;
        }  
        .custom-table th, .custom-table td {
            padding: 12px;
        }
        .custom-table thead {
            background-color: #f2f2f2;
            color: #333;
        }
        .custom-table tr {
            border-bottom: 1px solid #ddd;
        }
        /* Media Query untuk layar kecil */
        @media (max-width: 768px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font */
            }
        }
        @media (max-width: 480px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font lebih kecil */
            }
        }
        </style>';

        $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
        $output .= '<table class="custom-table">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Komiditi</th>';
        $output .= '<th style="padding:12px;">Luas Lahan (Ha)</th>';
        $output .= '<th style="padding:12px;">Ton/Tahun</th>';
                                    // Tambahkan kolom "Aksi" jika admin login
                                    if (current_user_can('administrator')) {
                                        $output .= '<th style="padding:12px;">Aksi</th>';
                                    }
        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->komiditi) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->luas_lahan) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->ton) . '</td>';
                                    // Jika admin login, tampilkan tombol Edit
                        if (current_user_can('administrator')) {
                            $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-data-tanaman-hortikultura?id=' . $row->id . '">Edit</a></td>';
            
                        }
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }
    if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-data-tanaman-hortikultura" class="tambah-button">Tambah Data Tanaman Hortikultura</a>';
        $output .= '</div>';
    }
    return $output;
}

function edit_data_tanaman_hortikultura() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $ppkbd_database = 'websitekecamatanbintantimur';
    $table_name = $ppkbd_database . '.data_tanaman_hortikultura'; // ppkbd tabel yang benar

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = sanitize_text_field($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_tanaman_hortikultura',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/data-potensi/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
            // Jika form disubmit untuk update
            if (isset($_POST['submit'])) {
                // Validasi dan sanitasi input dari form
                $komiditi = sanitize_text_field($_POST['komiditi']);
                $luas_lahan = floatval($_POST['luas_lahan']);
                $ton = floatval($_POST['ton']);

                // Update data ke database
                $wpdb->update(
                    'data_tanaman_hortikultura',
                    array(
                        'komiditi' => $komiditi,
                        'luas_lahan' => $luas_lahan,
                        'ton' => $ton,
                    ),
                    array('id' => $id),  // Syarat untuk update berdasarkan ID
                    array('%s', '%.2f', '%.2f'),  // Format tipe data
                    array('%d')  // Format untuk ID
                );

                // Redirect setelah data berhasil diupdate
                echo '<script type="text/javascript">
                    alert("Data berhasil diupdate.");
                    window.location.href = "http://websitekecamatanbintantimur.test/data-potensi/";
                </script>';
                exit;
            }

            // Tampilkan form untuk mengedit data
            return '
                <form method="post">
                    <label for="uraian">Nama Komiditi:</label>
                    <input type="text" name="komiditi" value="' . esc_attr($result->komiditi) . '"><br>
                    <label for="uraian">Luas Lahan:</label>
                    <input type="number" name="luas_lahan" value="' . esc_attr($result->luas_lahan) . '" step="0.01" inputmode="decimal" pattern="[0-9]+(\.[0-9]{1,2})?" /><br>
                    <label for="uraian">Berat (Ton):</label>
                    <input type="number" name="ton" value="' . esc_attr($result->ton) . '" step="0.01" inputmode="decimal" pattern="[0-9]+(\.[0-9]{1,2})?" /><br>
                    <input type="submit" name="submit" value="Update Data" class="update-button">
                    <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
                </form>';
        } else {
            // Jika data tidak ditemukan
            return '<p>Data tidak ditemukan.</p>';
        }
    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_data_tanaman_hortikultura', 'edit_data_tanaman_hortikultura');


function tambah_data_tanaman_hortikultura() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $ppkbd_database = 'websitekecamatanbintantimur';
    $table_name = $ppkbd_database . '.data_tanaman_hortikultura';

    if (isset($_POST['submit'])) {
        $komiditi = sanitize_text_field($_POST['komiditi']);
                $luas_lahan = floatval($_POST['luas_lahan']);
                $ton = floatval($_POST['ton']);

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_tanaman_hortikultura',
            array(
                'nomor' => $new_nomor,
                'komiditi' => $komiditi,
                        'luas_lahan' => $luas_lahan,
                        'ton' => $ton,
            ),
            array('%d', '%s', '%.2f', '%.2f')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/data-potensi/";
        </script>';
        exit;
    }

    return '
        <form method="post">
        <label for="uraian">Nama Komiditi:</label>
                    <input type="text" name="komiditi" value="' . esc_attr($result->komiditi) . '"><br>
                    <label for="uraian">Luas Lahan:</label>
                    <input type="number" name="luas_lahan" value="' . esc_attr($result->luas_lahan) . '" step="0.01" inputmode="decimal" pattern="[0-9]+(\.[0-9]{1,2})?" /><br>
                    <label for="uraian">Berat (Ton):</label>
                    <input type="number" name="ton" value="' . esc_attr($result->ton) . '" step="0.01" inputmode="decimal" pattern="[0-9]+(\.[0-9]{1,2})?" /><br>
        <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_tanaman_hortikultura', 'tambah_data_tanaman_hortikultura');


function   data_nelayan_penerima_bantuan_kelurahan_kijang_kota() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_nelayan_penerima_bantuan_kelurahan_kijang_kota';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
            text-align: left;
        }
        .custom-table th{
            font-size: 10px;
        }    
        .custom-table td{
            font-size: 10px;
        }  
        .custom-table th, .custom-table td {
            padding: 12px;
        }
        .custom-table thead {
            background-color: #f2f2f2;
            color: #333;
        }
        .custom-table tr {
            border-bottom: 1px solid #ddd;
        }
        /* Media Query untuk layar kecil */
        @media (max-width: 768px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font */
            }
        }
        @media (max-width: 480px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font lebih kecil */
            }
        }
        </style>';

        $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
        $output .= '<table class="custom-table">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Nama Kelompok</th>';
        $output .= '<th style="padding:12px;">Nama Penerima</th>';
        $output .= '<th style="padding:12px;">Alamat</th>';
        $output .= '<th style="padding:12px;">Jenis Barang Hibah</th>';
                                    // Tambahkan kolom "Aksi" jika admin login
                                    if (current_user_can('administrator')) {
                                        $output .= '<th style="padding:12px;">Aksi</th>';
                                    }
        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->nama_kelompok) . '</td>';
            $output .= '<td style="padding:12px;">' . format_text($row->nama_penerima) . '</td>';
            $output .= '<td style="padding:12px;">' . format_text($row->alamat) . '</td>';
            $output .= '<td style="padding:12px;">' . format_text($row->jenis_barang_hibah) . '</td>';
                                                // Jika admin login, tampilkan tombol Edit
                                                if (current_user_can('administrator')) {
                                                    $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-data-nelayan-penerima-bantuan-kelurahan-kijang-kota?id=' . $row->id . '">Edit</a></td>';
                                    
                                                }
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }
    if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-data-nelayan-penerima-bantuan-kelurahan-kijang-kota" class="tambah-button">Tambah Data Nelayan Penerima Bantuan Kelurahan Kijang Kota</a>';
        $output .= '</div>';
    }
    return $output;
}

function edit_data_nelayan_penerima_bantuan_kelurahan_kijang_kota() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $ppkbd_database = 'websitekecamatanbintantimur';
    $table_name = $ppkbd_database . '.data_nelayan_penerima_bantuan_kelurahan_kijang_kota'; // ppkbd tabel yang benar

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = sanitize_text_field($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_nelayan_penerima_bantuan_kelurahan_kijang_kota',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/data-potensi/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
            // Jika form disubmit untuk update
            if (isset($_POST['submit'])) {
                // Validasi dan sanitasi input dari form
                $nama_kelompok = sanitize_text_field($_POST['nama_kelompok']);
                $nama_penerima = sanitize_text_field($_POST['nama_penerima']);
                $alamat = sanitize_text_field($_POST['alamat']);
                $jenis_barang_hibah = sanitize_text_field($_POST['jenis_barang_hibah']);

                // Update data ke database
                $wpdb->update(
                    'data_nelayan_penerima_bantuan_kelurahan_kijang_kota',
                    array(
                        'nama_kelompok' => $nama_kelompok,
                        'nama_penerima' => $nama_penerima,
                        'alamat' => $alamat,
                        'jenis_barang_hibah'=> $jenis_barang_hibah,
                    ),
                    array('id' => $id),  // Syarat untuk update berdasarkan ID
                    array('%s', '%s', '%s', '%s'),  // Format tipe data
                    array('%d')  // Format untuk ID
                );

                // Redirect setelah data berhasil diupdate
                echo '<script type="text/javascript">
                    alert("Data berhasil diupdate.");
                    window.location.href = "http://websitekecamatanbintantimur.test/data-potensi/";
                </script>';
                exit;
            }

            // Tampilkan form untuk mengedit data
            return '
                <form method="post">
                    <label for="uraian">Nama Kelompok:</label>
                    <input type="text" name="nama_kelompok" value="' . esc_attr($result->nama_kelompok) . '"><br>
                    <label for="uraian">Nama Penerima:</label>
                    <input type="text" name="nama_penerima" value="' . esc_attr($result->nama_penerima) . '"><br>
                    <label for="uraian">Alamat:</label>
                    <input type="text" name="alamat" value="' . esc_attr($result->alamat) . '"><br>
                    <label for="uraian">Jenis Barang Hibah:</label>
                    <input type="text" name="jenis_barang_hibah" value="' . esc_attr($result->jenis_barang_hibah) . '"><br>
                    <br>
                    <input type="submit" name="submit" value="Update Data" class="update-button">
                    <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
                </form>';
        } else {
            // Jika data tidak ditemukan
            return '<p>Data tidak ditemukan.</p>';
        }
    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_data_nelayan_penerima_bantuan_kelurahan_kijang_kota', 'edit_data_nelayan_penerima_bantuan_kelurahan_kijang_kota');


function tambah_data_nelayan_penerima_bantuan_kelurahan_kijang_kota() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $ppkbd_database = 'websitekecamatanbintantimur';
    $table_name = $ppkbd_database . '.data_nelayan_penerima_bantuan_kelurahan_kijang_kota';

    if (isset($_POST['submit'])) {
        $nama_kelompok = sanitize_text_field($_POST['nama_kelompok']);
                $nama_penerima = sanitize_text_field($_POST['nama_penerima']);
                $alamat = sanitize_text_field($_POST['alamat']);
                $jenis_barang_hibah = sanitize_text_field($_POST['jenis_barang_hibah']);

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_nelayan_penerima_bantuan_kelurahan_kijang_kota',
            array(
                'nomor' => $new_nomor,
                'nama_kelompok' => $nama_kelompok,
                        'nama_penerima' => $nama_penerima,
                        'alamat' => $alamat,
                        'jenis_barang_hibah'=> $jenis_barang_hibah,
            ),
            array('%d', '%s', '%s', '%s', '%s')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/data-potensi/";
        </script>';
        exit;
    }

    return '
        <form method="post">
        <label for="uraian">Nama Kelompok:</label>
                    <input type="text" name="nama_kelompok" value="' . esc_attr($result->nama_kelompok) . '"><br>
                    <label for="uraian">Nama Penerima:</label>
                    <input type="text" name="nama_penerima" value="' . esc_attr($result->nama_penerima) . '"><br>
                    <label for="uraian">Alamat:</label>
                    <input type="text" name="alamat" value="' . esc_attr($result->alamat) . '"><br>
                    <label for="uraian">Jenis Barang Hibaht:</label>
                    <input type="text" name="jenis_barang_hibah" value="' . esc_attr($result->jenis_barang_hibah) . '"><br>
        <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_nelayan_penerima_bantuan_kelurahan_kijang_kota', 'tambah_data_nelayan_penerima_bantuan_kelurahan_kijang_kota');


function   data_nelayan_penerima_bantuan_kelurahan_sungai_enam() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_nelayan_penerima_bantuan_kelurahan_sungai_enam';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
            text-align: left;
        }
        .custom-table th{
            font-size: 10px;
        }    
        .custom-table td{
            font-size: 10px;
        }  
        .custom-table th, .custom-table td {
            padding: 12px;
        }
        .custom-table thead {
            background-color: #f2f2f2;
            color: #333;
        }
        .custom-table tr {
            border-bottom: 1px solid #ddd;
        }
        /* Media Query untuk layar kecil */
        @media (max-width: 768px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font */
            }
        }
        @media (max-width: 480px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font lebih kecil */
            }
        }
        </style>';

        $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
        $output .= '<table class="custom-table">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Nama Kelompok</th>';
        $output .= '<th style="padding:12px;">Nama Penerima</th>';
        $output .= '<th style="padding:12px;">Alamat</th>';
        $output .= '<th style="padding:12px;">Jenis Barang Hibah</th>';
        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->nama_kelompok) . '</td>';
            $output .= '<td style="padding:12px;">' . format_text($row->nama_penerima) . '</td>';
            $output .= '<td style="padding:12px;">' . format_text($row->alamat) . '</td>';
            $output .= '<td style="padding:12px;">' . format_text($row->jenis_barang_hibah) . '</td>';
                                                // Jika admin login, tampilkan tombol Edit
                                                if (current_user_can('administrator')) {
                                                    $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-data-nelayan-penerima-bantuan-kelurahan-sungai-enam?id=' . $row->id . '">Edit</a></td>';
                                    
                                                }
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }
    if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-data-nelayan-penerima-bantuan-kelurahan-sungai-enam" class="tambah-button">Tambah Data Nelayan Penerima Bantuan Kelurahan Sungai Enam</a>';
        $output .= '</div>';
    }
    return $output;
}

function edit_data_nelayan_penerima_bantuan_kelurahan_sungai_enam() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $ppkbd_database = 'websitekecamatanbintantimur';
    $table_name = $ppkbd_database . '.data_nelayan_penerima_bantuan_kelurahan_sungai_enam'; // ppkbd tabel yang benar

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = sanitize_text_field($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_nelayan_penerima_bantuan_kelurahan_sungai_enam',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/data-potensi/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
            // Jika form disubmit untuk update
            if (isset($_POST['submit'])) {
                // Validasi dan sanitasi input dari form
                $nama_kelompok = sanitize_text_field($_POST['nama_kelompok']);
                $nama_penerima = sanitize_text_field($_POST['nama_penerima']);
                $alamat = sanitize_text_field($_POST['alamat']);
                $jenis_barang_hibah = sanitize_text_field($_POST['jenis_barang_hibah']);

                // Update data ke database
                $wpdb->update(
                    'data_nelayan_penerima_bantuan_kelurahan_sungai_enam',
                    array(
                        'nama_kelompok' => $nama_kelompok,
                        'nama_penerima' => $nama_penerima,
                        'alamat' => $alamat,
                        'jenis_barang_hibah'=> $jenis_barang_hibah,
                    ),
                    array('id' => $id),  // Syarat untuk update berdasarkan ID
                    array('%s', '%s', '%s', '%s'),  // Format tipe data
                    array('%d')  // Format untuk ID
                );

                // Redirect setelah data berhasil diupdate
                echo '<script type="text/javascript">
                    alert("Data berhasil diupdate.");
                    window.location.href = "http://websitekecamatanbintantimur.test/data-potensi/";
                </script>';
                exit;
            }

            // Tampilkan form untuk mengedit data
            return '
                <form method="post">
                    <label for="uraian">Nama Kelompok:</label>
                    <input type="text" name="nama_kelompok" value="' . esc_attr($result->nama_kelompok) . '"><br>
                    <label for="uraian">Nama Penerima:</label>
                    <input type="text" name="nama_penerima" value="' . esc_attr($result->nama_penerima) . '"><br>
                    <label for="uraian">Alamat:</label>
                    <input type="text" name="alamat" value="' . esc_attr($result->alamat) . '"><br>
                    <label for="uraian">Jenis Barang Hibaht:</label>
                    <input type="text" name="jenis_barang_hibah" value="' . esc_attr($result->jenis_barang_hibah) . '"><br>
                    <input type="submit" name="submit" value="Update Data" class="update-button">
                    <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
                </form>';
        } else {
            // Jika data tidak ditemukan
            return '<p>Data tidak ditemukan.</p>';
        }
    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_data_nelayan_penerima_bantuan_kelurahan_sungai_enam', 'edit_data_nelayan_penerima_bantuan_kelurahan_sungai_enam');


function tambah_data_nelayan_penerima_bantuan_kelurahan_sungai_enam() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $ppkbd_database = 'websitekecamatanbintantimur';
    $table_name = $ppkbd_database . '.data_nelayan_penerima_bantuan_kelurahan_sungai_enam';

    if (isset($_POST['submit'])) {
        $nama_kelompok = sanitize_text_field($_POST['nama_kelompok']);
                $nama_penerima = sanitize_text_field($_POST['nama_penerima']);
                $alamat = sanitize_text_field($_POST['alamat']);
                $jenis_barang_hibah = sanitize_text_field($_POST['jenis_barang_hibah']);

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_nelayan_penerima_bantuan_kelurahan_sungai_enam',
            array(
                'nomor' => $new_nomor,
                'nama_kelompok' => $nama_kelompok,
                        'nama_penerima' => $nama_penerima,
                        'alamat' => $alamat,
                        'jenis_barang_hibah'=> $jenis_barang_hibah,
            ),
            array('%d', '%s', '%s', '%s', '%s')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/data-potensi/";
        </script>';
        exit;
    }

    return '
        <form method="post">
        <label for="uraian">Nama Kelompok:</label>
                    <input type="text" name="nama_kelompok" value="' . esc_attr($result->nama_kelompok) . '"><br>
                    <label for="uraian">Nama Penerima:</label>
                    <input type="text" name="nama_penerima" value="' . esc_attr($result->nama_penerima) . '"><br>
                    <label for="uraian">Alamat:</label>
                    <input type="text" name="alamat" value="' . esc_attr($result->alamat) . '"><br>
                    <label for="uraian">Jenis Barang Hibah:</label>
                    <input type="text" name="jenis_barang_hibah" value="' . esc_attr($result->jenis_barang_hibah) . '"><br>
        <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_nelayan_penerima_bantuan_kelurahan_sungai_enam', 'tambah_data_nelayan_penerima_bantuan_kelurahan_sungai_enam');


function   data_nelayan_penerima_bantuan_kelurahan_sungai_lekop() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_nelayan_penerima_bantuan_kelurahan_sungai_lekop';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
            text-align: left;
        }
        .custom-table th{
            font-size: 10px;
        }    
        .custom-table td{
            font-size: 10px;
        }  
        .custom-table th, .custom-table td {
            padding: 12px;
        }
        .custom-table thead {
            background-color: #f2f2f2;
            color: #333;
        }
        .custom-table tr {
            border-bottom: 1px solid #ddd;
        }
        /* Media Query untuk layar kecil */
        @media (max-width: 768px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font */
            }
        }
        @media (max-width: 480px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font lebih kecil */
            }
        }
        </style>';

        $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
        $output .= '<table class="custom-table">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Nama Kelompok</th>';
        $output .= '<th style="padding:12px;">Nama Penerima</th>';
        $output .= '<th style="padding:12px;">Alamat</th>';
        $output .= '<th style="padding:12px;">Jenis Barang Hibah</th>';
        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->nama_kelompok) . '</td>';
            $output .= '<td style="padding:12px;">' . format_text($row->nama_penerima) . '</td>';
            $output .= '<td style="padding:12px;">' . format_text($row->alamat) . '</td>';
            $output .= '<td style="padding:12px;">' . format_text($row->jenis_barang_hibah) . '</td>';
                                    // Jika admin login, tampilkan tombol Edit
                                    if (current_user_can('administrator')) {
                                        $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-data-nelayan-penerima-bantuan-kelurahan-sungai-lekop?id=' . $row->id . '">Edit</a></td>';
                        
                                    }
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }
    if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-data-nelayan-penerima-bantuan-kelurahan-sungai-lekop" class="tambah-button">Tambah Data Nelayan Penerima Bantuan Kelurahan Sungai Lekop</a>';
        $output .= '</div>';
    }
    return $output;
}

function edit_data_nelayan_penerima_bantuan_kelurahan_sungai_lekop() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $ppkbd_database = 'websitekecamatanbintantimur';
    $table_name = $ppkbd_database . '.data_nelayan_penerima_bantuan_kelurahan_sungai_lekop'; // ppkbd tabel yang benar

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = sanitize_text_field($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_nelayan_penerima_bantuan_kelurahan_sungai_lekop',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/data-potensi/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
            // Jika form disubmit untuk update
            if (isset($_POST['submit'])) {
                // Validasi dan sanitasi input dari form
                $nama_kelompok = sanitize_text_field($_POST['nama_kelompok']);
                $nama_penerima = sanitize_text_field($_POST['nama_penerima']);
                $alamat = sanitize_text_field($_POST['alamat']);
                $jenis_barang_hibah = sanitize_text_field($_POST['jenis_barang_hibah']);

                // Update data ke database
                $wpdb->update(
                    'data_nelayan_penerima_bantuan_kelurahan_sungai_lekop',
                    array(
                        'nama_kelompok' => $nama_kelompok,
                        'nama_penerima' => $nama_penerima,
                        'alamat' => $alamat,
                        'jenis_barang_hibah'=> $jenis_barang_hibah,
                    ),
                    array('id' => $id),  // Syarat untuk update berdasarkan ID
                    array('%s', '%s', '%s', '%s'),  // Format tipe data
                    array('%d')  // Format untuk ID
                );

                // Redirect setelah data berhasil diupdate
                echo '<script type="text/javascript">
                    alert("Data berhasil diupdate.");
                    window.location.href = "http://websitekecamatanbintantimur.test/data-potensi/";
                </script>';
                exit;
            }

            // Tampilkan form untuk mengedit data
            return '
                <form method="post">
                    <label for="uraian">Nama Kelompok:</label>
                    <input type="text" name="nama_kelompok" value="' . esc_attr($result->nama_kelompok) . '"><br>
                    <label for="uraian">Nama Penerima:</label>
                    <input type="text" name="nama_penerima" value="' . esc_attr($result->nama_penerima) . '"><br>
                    <label for="uraian">Alamat:</label>
                    <input type="text" name="alamat" value="' . esc_attr($result->alamat) . '"><br>
                    <label for="uraian">Jenis Barang Hibah:</label>
                    <input type="text" name="jenis_barang_hibah" value="' . esc_attr($result->jenis_barang_hibah) . '"><br>
                    <input type="submit" name="submit" value="Update Data" class="update-button">
                    <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
                </form>';
        } else {
            // Jika data tidak ditemukan
            return '<p>Data tidak ditemukan.</p>';
        }
    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_data_nelayan_penerima_bantuan_kelurahan_sungai_lekop', 'edit_data_nelayan_penerima_bantuan_kelurahan_sungai_lekop');


function tambah_data_nelayan_penerima_bantuan_kelurahan_sungai_lekop() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $ppkbd_database = 'websitekecamatanbintantimur';
    $table_name = $ppkbd_database . '.data_nelayan_penerima_bantuan_kelurahan_sungai_lekop';

    if (isset($_POST['submit'])) {
        $nama_kelompok = sanitize_text_field($_POST['nama_kelompok']);
                $nama_penerima = sanitize_text_field($_POST['nama_penerima']);
                $alamat = sanitize_text_field($_POST['alamat']);
                $jenis_barang_hibah = sanitize_text_field($_POST['jenis_barang_hibah']);

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_nelayan_penerima_bantuan_kelurahan_sungai_lekop',
            array(
                'nomor' => $new_nomor,
                'nama_kelompok' => $nama_kelompok,
                        'nama_penerima' => $nama_penerima,
                        'alamat' => $alamat,
                        'jenis_barang_hibah'=> $jenis_barang_hibah,
            ),
            array('%d', '%s', '%s', '%s', '%s')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/data-potensi/";
        </script>';
        exit;
    }

    return '
        <form method="post">
        <label for="uraian">Nama Kelompok:</label>
                    <input type="text" name="nama_kelompok" value="' . esc_attr($result->nama_kelompok) . '"><br>
                    <label for="uraian">Nama Penerima:</label>
                    <input type="text" name="nama_penerima" value="' . esc_attr($result->nama_penerima) . '"><br>
                    <label for="uraian">Alamat:</label>
                    <input type="text" name="alamat" value="' . esc_attr($result->alamat) . '"><br>
                    <label for="uraian">Jenis Barang Hibah:</label>
                    <input type="text" name="jenis_barang_hibah" value="' . esc_attr($result->jenis_barang_hibah) . '"><br>
        <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_nelayan_penerima_bantuan_kelurahan_sungai_lekop', 'tambah_data_nelayan_penerima_bantuan_kelurahan_sungai_lekop');

function   data_peternakan_ayam_buras() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_peternakan_ayam_buras';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
            text-align: left;
        }
        .custom-table th{
            font-size: 10px;
        }    
        .custom-table td{
            font-size: 10px;
        }  
        .custom-table th, .custom-table td {
            padding: 12px;
        }
        .custom-table thead {
            background-color: #f2f2f2;
            color: #333;
        }
        .custom-table tr {
            border-bottom: 1px solid #ddd;
        }
        /* Media Query untuk layar kecil */
        @media (max-width: 768px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font */
            }
        }
        @media (max-width: 480px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font lebih kecil */
            }
        }
        </style>';

        $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
        $output .= '<table class="custom-table">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Nama Pemilik Ternak</th>';
        $output .= '<th style="padding:12px;">Jenis Ternak</th>';
        $output .= '<th style="padding:12px;">Jumlah (Ekor)</th>';
        $output .= '<th style="padding:12px;">Alamat Peternakan</th>';
        $output .= '<th style="padding:12px;">Ket</th>';
                                   // Tambahkan kolom "Aksi" jika admin login
                                   if (current_user_can('administrator')) {
                                    $output .= '<th style="padding:12px;">Aksi</th>';
                                }
        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->nama_pemilik_ternak) . '</td>';
            $output .= '<td style="padding:12px;">' . format_text($row->jenis_ternak) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->jumlah_ekor) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->alamat_peternak) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->ket) . '</td>';
                                    // Jika admin login, tampilkan tombol Edit
                                    if (current_user_can('administrator')) {
                                        $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-data-peternakan-ayam-buras?id=' . $row->id . '">Edit</a></td>';
                        
                                    }
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }
    if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-data-peternakan-ayam-buras" class="tambah-button">Tambah Data Peternakan Ayam Buras</a>';
        $output .= '</div>';
    }
    return $output;
}

function edit_data_peternakan_ayam_buras() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $ppkbd_database = 'websitekecamatanbintantimur';
    $table_name = $ppkbd_database . '.data_peternakan_ayam_buras'; // ppkbd tabel yang benar

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = sanitize_text_field($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_peternakan_ayam_buras',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/data-potensi/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
            // Jika form disubmit untuk update
            if (isset($_POST['submit'])) {
                // Validasi dan sanitasi input dari form
                $nama_pemilik_ternak = sanitize_text_field($_POST['nama_pemilik_ternak']);
                $jenis_ternak = sanitize_text_field($_POST['jenis_ternak']);
                $jumlah_ekor = intval($_POST['jumlah_ekor']);
                $alamat_peternak = sanitize_text_field($_POST['alamat_peternak']);
                $ket = sanitize_text_field($_POST['ket']);

                // Update data ke database
                $wpdb->update(
                    'data_peternakan_ayam_buras',
                    array(
                        'nama_pemilik_ternak' => $nama_pemilik_ternak,
                        'jenis_ternak' => $jenis_ternak,
                        'jumlah_ekor' => $jumlah_ekor,
                        'alamat_peternak' => $alamat_peternak,
                        'ket'=> $ket,
                    ),
                    array('id' => $id),  // Syarat untuk update berdasarkan ID
                    array('%s', '%s', '%d', '%s', '%s'),  // Format tipe data
                    array('%d')  // Format untuk ID
                );

                // Redirect setelah data berhasil diupdate
                echo '<script type="text/javascript">
                    alert("Data berhasil diupdate.");
                    window.location.href = "http://websitekecamatanbintantimur.test/data-potensi/";
                </script>';
                exit;
            }

            // Tampilkan form untuk mengedit data
            return '
                <form method="post">
                    <label for="uraian">Nama Pemilik Ternak:</label>
                    <input type="text" name="nama_pemilik_ternak" value="' . esc_attr($result->nama_pemilik_ternak) . '"><br>
                    <label for="uraian">Jenis Ternak:</label>
                    <input type="text" name="jenis_ternak" value="' . esc_attr($result->jenis_ternak) . '"><br>
                    <label for="uraian">Jumlah Ekor:</label>
                    <input type="number" name="jumlah_ekor" value="' . esc_attr($result->jumlah_ekor) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Alamat Peternak:</label>
                    <input type="text" name="alamat_peternak" value="' . esc_attr($result->alamat_peternak) . '"><br>
                    <label for="uraian">Keterangan:</label>
                    <input type="text" name="ket" value="' . esc_attr($result->ket) . '"><br>
                    <input type="submit" name="submit" value="Update Data" class="update-button">
                    <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
                </form>';
        } else {
            // Jika data tidak ditemukan
            return '<p>Data tidak ditemukan.</p>';
        }
    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_data_peternakan_ayam_buras', 'edit_data_peternakan_ayam_buras');


function tambah_data_peternakan_ayam_buras() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $ppkbd_database = 'websitekecamatanbintantimur';
    $table_name = $ppkbd_database . '.data_peternakan_ayam_buras';

    if (isset($_POST['submit'])) {
        $nama_pemilik_ternak = sanitize_text_field($_POST['nama_pemilik_ternak']);
                $jenis_ternak = sanitize_text_field($_POST['jenis_ternak']);
                $jumlah_ekor = intval($_POST['jumlah_ekor']);
                $alamat_peternak = sanitize_text_field($_POST['alamat_peternak']);
                $ket = sanitize_text_field($_POST['ket']);

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_peternakan_ayam_buras',
            array(
                'nomor' => $new_nomor,
                'nama_pemilik_ternak' => $nama_pemilik_ternak,
                        'jenis_ternak' => $jenis_ternak,
                        'jumlah_ekor' => $jumlah_ekor,
                        'alamat_peternak' => $alamat_peternak,
                        'ket'=> $ket,
            ),
            array('%d', '%s', '%s', '%d', '%s', '%s')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/data-potensi/";
        </script>';
        exit;
    }

    return '
        <form method="post">
        <label for="uraian">Nama Pemilik Ternak:</label>
                    <input type="text" name="nama_pemilik_ternak" value="' . esc_attr($result->nama_pemilik_ternak) . '"><br>
                    <label for="uraian">Jenis Ternak:</label>
                    <input type="text" name="jenis_ternak" value="' . esc_attr($result->jenis_ternak) . '"><br>
                    <label for="uraian">Jumlah Ekor:</label>
                    <input type="number" name="jumlah_ekor" value="' . esc_attr($result->jumlah_ekor) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Alamat Peternak:</label>
                    <input type="text" name="alamat_peternak" value="' . esc_attr($result->alamat_peternak) . '"><br>
                    <label for="uraian">Keterangan:</label>
                    <input type="text" name="ket" value="' . esc_attr($result->ket) . '"><br>
         <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_peternakan_ayam_buras', 'tambah_data_peternakan_ayam_buras');


function   data_peternakan_ayam_broiler() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_peternakan_ayam_broiler';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
            text-align: left;
        }
        .custom-table th{
            font-size: 10px;
        }    
        .custom-table td{
            font-size: 10px;
        }  
        .custom-table th, .custom-table td {
            padding: 12px;
        }
        .custom-table thead {
            background-color: #f2f2f2;
            color: #333;
        }
        .custom-table tr {
            border-bottom: 1px solid #ddd;
        }
        /* Media Query untuk layar kecil */
        @media (max-width: 768px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font */
            }
        }
        @media (max-width: 480px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font lebih kecil */
            }
        }
        </style>';

        $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
        $output .= '<table class="custom-table">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Nama Pemilik Ternak</th>';
        $output .= '<th style="padding:12px;">Jenis Ternak</th>';
        $output .= '<th style="padding:12px;">Jumlah (Ekor)</th>';
        $output .= '<th style="padding:12px;">Alamat Peternakan</th>';
        $output .= '<th style="padding:12px;">Ket</th>';
                                   // Tambahkan kolom "Aksi" jika admin login
                                   if (current_user_can('administrator')) {
                                    $output .= '<th style="padding:12px;">Aksi</th>';
                                }
        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->nama_pemilik_ternak) . '</td>';
            $output .= '<td style="padding:12px;">' . format_text($row->jenis_ternak) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->jumlah_ekor) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->alamat_peternak) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->ket) . '</td>';
                                    // Jika admin login, tampilkan tombol Edit
                                    if (current_user_can('administrator')) {
                                        $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-data-peternakan-ayam-broiler?id=' . $row->id . '">Edit</a></td>';
                        
                                    }
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }
    if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-data-peternakan-ayam-broiler" class="tambah-button">Tambah Data Peternakan Ayam Broiler</a>';
        $output .= '</div>';
    }
    return $output;
}

function edit_data_peternakan_ayam_broiler() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $ppkbd_database = 'websitekecamatanbintantimur';
    $table_name = $ppkbd_database . '.data_peternakan_ayam_broiler'; // ppkbd tabel yang benar

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = sanitize_text_field($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_peternakan_ayam_broiler',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/data-potensi/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
            // Jika form disubmit untuk update
            if (isset($_POST['submit'])) {
                // Validasi dan sanitasi input dari form
                $nama_pemilik_ternak = sanitize_text_field($_POST['nama_pemilik_ternak']);
                $jenis_ternak = sanitize_text_field($_POST['jenis_ternak']);
                $jumlah_ekor = intval($_POST['jumlah_ekor']);
                $alamat_peternak = sanitize_text_field($_POST['alamat_peternak']);
                $ket = sanitize_text_field($_POST['ket']);

                // Update data ke database
                $wpdb->update(
                    'data_peternakan_ayam_broiler',
                    array(
                        'nama_pemilik_ternak' => $nama_pemilik_ternak,
                        'jenis_ternak' => $jenis_ternak,
                        'jumlah_ekor' => $jumlah_ekor,
                        'alamat_peternak' => $alamat_peternak,
                        'ket'=> $ket,
                    ),
                    array('id' => $id),  // Syarat untuk update berdasarkan ID
                    array('%s', '%s', '%d', '%s', '%s'),  // Format tipe data
                    array('%d')  // Format untuk ID
                );

                // Redirect setelah data berhasil diupdate
                echo '<script type="text/javascript">
                    alert("Data berhasil diupdate.");
                    window.location.href = "http://websitekecamatanbintantimur.test/data-potensi/";
                </script>';
                exit;
            }

            // Tampilkan form untuk mengedit data
            return '
                <form method="post">
                    <label for="uraian">Nama Pemilik Ternak:</label>
                    <input type="text" name="nama_pemilik_ternak" value="' . esc_attr($result->nama_pemilik_ternak) . '"><br>
                    <label for="uraian">Jenis Ternak:</label>
                    <input type="text" name="jenis_ternak" value="' . esc_attr($result->jenis_ternak) . '"><br>
                    <label for="uraian">Jumlah Ekor:</label>
                    <input type="number" name="jumlah_ekor" value="' . esc_attr($result->jumlah_ekor) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Alamat Peternak:</label>
                    <input type="text" name="alamat_peternak" value="' . esc_attr($result->alamat_peternak) . '"><br>
                    <label for="uraian">Keterangan:</label>
                    <input type="text" name="ket" value="' . esc_attr($result->ket) . '"><br>
                    <input type="submit" name="submit" value="Update Data" class="update-button">
                    <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
                </form>';
        } else {
            // Jika data tidak ditemukan
            return '<p>Data tidak ditemukan.</p>';
        }
    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_data_peternakan_ayam_broiler', 'edit_data_peternakan_ayam_broiler');


function tambah_data_peternakan_ayam_broiler() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $ppkbd_database = 'websitekecamatanbintantimur';
    $table_name = $ppkbd_database . '.data_peternakan_ayam_broiler';

    if (isset($_POST['submit'])) {
        $nama_pemilik_ternak = sanitize_text_field($_POST['nama_pemilik_ternak']);
                $jenis_ternak = sanitize_text_field($_POST['jenis_ternak']);
                $jumlah_ekor = intval($_POST['jumlah_ekor']);
                $alamat_peternak = sanitize_text_field($_POST['alamat_peternak']);
                $ket = sanitize_text_field($_POST['ket']);

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_peternakan_ayam_broiler',
            array(
                'nomor' => $new_nomor,
                'nama_pemilik_ternak' => $nama_pemilik_ternak,
                        'jenis_ternak' => $jenis_ternak,
                        'jumlah_ekor' => $jumlah_ekor,
                        'alamat_peternak' => $alamat_peternak,
                        'ket'=> $ket,
            ),
            array('%d', '%s', '%s', '%d', '%s', '%s')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/data-potensi/";
        </script>';
        exit;
    }

    return '
        <form method="post">
        <label for="uraian">Nama Pemilik Ternak:</label>
                    <input type="text" name="nama_pemilik_ternak" value="' . esc_attr($result->nama_pemilik_ternak) . '"><br>
                    <label for="uraian">Jenis Ternak:</label>
                    <input type="text" name="jenis_ternak" value="' . esc_attr($result->jenis_ternak) . '"><br>
                    <label for="uraian">Jumlah Ekor:</label>
                    <input type="number" name="jumlah_ekor" value="' . esc_attr($result->jumlah_ekor) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Alamat Peternak:</label>
                    <input type="text" name="alamat_peternak" value="' . esc_attr($result->alamat_peternak) . '"><br>
                    <label for="uraian">Keterangan:</label>
                    <input type="text" name="ket" value="' . esc_attr($result->ket) . '"><br>
         <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_peternakan_ayam_broiler', 'tambah_data_peternakan_ayam_broiler');

function   data_peternakan_itik() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_peternakan_itik';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
            text-align: left;
        }
        .custom-table th{
            font-size: 10px;
        }    
        .custom-table td{
            font-size: 10px;
        }  
        .custom-table th, .custom-table td {
            padding: 12px;
        }
        .custom-table thead {
            background-color: #f2f2f2;
            color: #333;
        }
        .custom-table tr {
            border-bottom: 1px solid #ddd;
        }
        /* Media Query untuk layar kecil */
        @media (max-width: 768px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font */
            }
        }
        @media (max-width: 480px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font lebih kecil */
            }
        }
        </style>';

        $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
        $output .= '<table class="custom-table">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Nama Pemilik Ternak</th>';
        $output .= '<th style="padding:12px;">Jenis Ternak</th>';
        $output .= '<th style="padding:12px;">Jumlah (Ekor)</th>';
        $output .= '<th style="padding:12px;">Alamat Peternakan</th>';
        $output .= '<th style="padding:12px;">Ket</th>';
                                   // Tambahkan kolom "Aksi" jika admin login
                                   if (current_user_can('administrator')) {
                                    $output .= '<th style="padding:12px;">Aksi</th>';
                                }
        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->nama_pemilik_ternak) . '</td>';
            $output .= '<td style="padding:12px;">' . format_text($row->jenis_ternak) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->jumlah_ekor) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->alamat_peternak) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->ket) . '</td>';
                                    // Jika admin login, tampilkan tombol Edit
                                    if (current_user_can('administrator')) {
                                        $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-data-peternakan-itik?id=' . $row->id . '">Edit</a></td>';
                        
                                    }
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }
    if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-data-peternakan-itik" class="tambah-button">Tambah Data Peternakan Itik</a>';
        $output .= '</div>';
    }

    return $output;
}

function edit_data_peternakan_itik() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $ppkbd_database = 'websitekecamatanbintantimur';
    $table_name = $ppkbd_database . '.data_peternakan_itik'; // ppkbd tabel yang benar

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = sanitize_text_field($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_peternakan_itik',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/data-potensi/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
            // Jika form disubmit untuk update
            if (isset($_POST['submit'])) {
                // Validasi dan sanitasi input dari form
                $nama_pemilik_ternak = sanitize_text_field($_POST['nama_pemilik_ternak']);
                $jenis_ternak = sanitize_text_field($_POST['jenis_ternak']);
                $jumlah_ekor = intval($_POST['jumlah_ekor']);
                $alamat_peternak = sanitize_text_field($_POST['alamat_peternak']);
                $ket = sanitize_text_field($_POST['ket']);

                // Update data ke database
                $wpdb->update(
                    'data_peternakan_itik',
                    array(
                        'nama_pemilik_ternak' => $nama_pemilik_ternak,
                        'jenis_ternak' => $jenis_ternak,
                        'jumlah_ekor' => $jumlah_ekor,
                        'alamat_peternak' => $alamat_peternak,
                        'ket'=> $ket,
                    ),
                    array('id' => $id),  // Syarat untuk update berdasarkan ID
                    array('%s', '%s', '%d', '%s', '%s'),  // Format tipe data
                    array('%d')  // Format untuk ID
                );

                // Redirect setelah data berhasil diupdate
                echo '<script type="text/javascript">
                    alert("Data berhasil diupdate.");
                    window.location.href = "http://websitekecamatanbintantimur.test/data-potensi/";
                </script>';
                exit;
            }

            // Tampilkan form untuk mengedit data
            return '
                <form method="post">
                    <label for="uraian">Nama Pemilik Ternak:</label>
                    <input type="text" name="nama_pemilik_ternak" value="' . esc_attr($result->nama_pemilik_ternak) . '"><br>
                    <label for="uraian">Jenis Ternak:</label>
                    <input type="text" name="jenis_ternak" value="' . esc_attr($result->jenis_ternak) . '"><br>
                    <label for="uraian">Jumlah Ekor:</label>
                    <input type="number" name="jumlah_ekor" value="' . esc_attr($result->jumlah_ekor) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Alamat Peternak:</label>
                    <input type="text" name="alamat_peternak" value="' . esc_attr($result->alamat_peternak) . '"><br>
                    <label for="uraian">Keterangan:</label>
                    <input type="text" name="ket" value="' . esc_attr($result->ket) . '"><br>
                    <input type="submit" name="submit" value="Update Data" class="update-button">
                    <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
                </form>';
        } else {
            // Jika data tidak ditemukan
            return '<p>Data tidak ditemukan.</p>';
        }
    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_data_peternakan_itik', 'edit_data_peternakan_itik');


function tambah_data_peternakan_itik() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $ppkbd_database = 'websitekecamatanbintantimur';
    $table_name = $ppkbd_database . '.data_peternakan_itik';

    if (isset($_POST['submit'])) {
        $nama_pemilik_ternak = sanitize_text_field($_POST['nama_pemilik_ternak']);
                $jenis_ternak = sanitize_text_field($_POST['jenis_ternak']);
                $jumlah_ekor = intval($_POST['jumlah_ekor']);
                $alamat_peternak = sanitize_text_field($_POST['alamat_peternak']);
                $ket = sanitize_text_field($_POST['ket']);

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_peternakan_itik',
            array(
                'nomor' => $new_nomor,
                'nama_pemilik_ternak' => $nama_pemilik_ternak,
                        'jenis_ternak' => $jenis_ternak,
                        'jumlah_ekor' => $jumlah_ekor,
                        'alamat_peternak' => $alamat_peternak,
                        'ket'=> $ket,
            ),
            array('%d', '%s', '%s', '%d', '%s', '%s')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/data-potensi/";
        </script>';
        exit;
    }

    return '
        <form method="post">
        <label for="uraian">Nama Pemilik Ternak:</label>
                    <input type="text" name="nama_pemilik_ternak" value="' . esc_attr($result->nama_pemilik_ternak) . '"><br>
                    <label for="uraian">Jenis Ternak:</label>
                    <input type="text" name="jenis_ternak" value="' . esc_attr($result->jenis_ternak) . '"><br>
                    <label for="uraian">Jumlah Ekor:</label>
                    <input type="number" name="jumlah_ekor" value="' . esc_attr($result->jumlah_ekor) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Alamat Peternak:</label>
                    <input type="text" name="alamat_peternak" value="' . esc_attr($result->alamat_peternak) . '"><br>
                    <label for="uraian">Keterangan:</label>
                    <input type="text" name="ket" value="' . esc_attr($result->ket) . '"><br>
         <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_peternakan_itik', 'tambah_data_peternakan_itik');

function   data_peternakan_sapi() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_peternakan_sapi';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
            text-align: left;
        }
        .custom-table th{
            font-size: 10px;
        }    
        .custom-table td{
            font-size: 10px;
        }  
        .custom-table th, .custom-table td {
            padding: 12px;
        }
        .custom-table thead {
            background-color: #f2f2f2;
            color: #333;
        }
        .custom-table tr {
            border-bottom: 1px solid #ddd;
        }
        /* Media Query untuk layar kecil */
        @media (max-width: 768px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font */
            }
        }
        @media (max-width: 480px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font lebih kecil */
            }
        }
        </style>';

        $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
        $output .= '<table class="custom-table">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Nama Pemilik Ternak</th>';
        $output .= '<th style="padding:12px;">Jenis Ternak</th>';
        $output .= '<th style="padding:12px;">Jumlah (Ekor)</th>';
        $output .= '<th style="padding:12px;">Alamat Peternakan</th>';
        $output .= '<th style="padding:12px;">Ket</th>';
                                   // Tambahkan kolom "Aksi" jika admin login
                                   if (current_user_can('administrator')) {
                                    $output .= '<th style="padding:12px;">Aksi</th>';
                                }
        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->nama_pemilik_ternak) . '</td>';
            $output .= '<td style="padding:12px;">' . format_text($row->jenis_ternak) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->jumlah_ekor) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->alamat_peternak) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->ket) . '</td>';
                                                // Jika admin login, tampilkan tombol Edit
                                                if (current_user_can('administrator')) {
                                                    $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-data-peternakan-sapi?id=' . $row->id . '">Edit</a></td>';
                                    
                                                }
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }
    if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-data-peternakan-sapi" class="tambah-button">Tambah Data Peternakan Sapi</a>';
        $output .= '</div>';
    }
    return $output;
}

function edit_data_peternakan_sapi() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $ppkbd_database = 'websitekecamatanbintantimur';
    $table_name = $ppkbd_database . '.data_peternakan_sapi'; // ppkbd tabel yang benar

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = sanitize_text_field($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_peternakan_sapi',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/data-potensi/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
            // Jika form disubmit untuk update
            if (isset($_POST['submit'])) {
                // Validasi dan sanitasi input dari form
                $nama_pemilik_ternak = sanitize_text_field($_POST['nama_pemilik_ternak']);
                $jenis_ternak = sanitize_text_field($_POST['jenis_ternak']);
                $jumlah_ekor = intval($_POST['jumlah_ekor']);
                $alamat_peternak = sanitize_text_field($_POST['alamat_peternak']);
                $ket = sanitize_text_field($_POST['ket']);

                // Update data ke database
                $wpdb->update(
                    'data_peternakan_sapi',
                    array(
                        'nama_pemilik_ternak' => $nama_pemilik_ternak,
                        'jenis_ternak' => $jenis_ternak,
                        'jumlah_ekor' => $jumlah_ekor,
                        'alamat_peternak' => $alamat_peternak,
                        'ket'=> $ket,
                    ),
                    array('id' => $id),  // Syarat untuk update berdasarkan ID
                    array('%s', '%s', '%d', '%s', '%s'),  // Format tipe data
                    array('%d')  // Format untuk ID
                );

                // Redirect setelah data berhasil diupdate
                echo '<script type="text/javascript">
                    alert("Data berhasil diupdate.");
                    window.location.href = "http://websitekecamatanbintantimur.test/data-potensi/";
                </script>';
                exit;
            }

            // Tampilkan form untuk mengedit data
            return '
                <form method="post">
                    <label for="uraian">Nama Pemilik Ternak:</label>
                    <input type="text" name="nama_pemilik_ternak" value="' . esc_attr($result->nama_pemilik_ternak) . '"><br>
                    <label for="uraian">Jenis Ternak:</label>
                    <input type="text" name="jenis_ternak" value="' . esc_attr($result->jenis_ternak) . '"><br>
                    <label for="uraian">Jumlah Ekor:</label>
                    <input type="number" name="jumlah_ekor" value="' . esc_attr($result->jumlah_ekor) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Alamat Peternak:</label>
                    <input type="text" name="alamat_peternak" value="' . esc_attr($result->alamat_peternak) . '"><br>
                    <label for="uraian">Keterangan:</label>
                    <input type="text" name="ket" value="' . esc_attr($result->ket) . '"><br>
                    <input type="submit" name="submit" value="Update Data" class="update-button">
                    <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
                </form>';
        } else {
            // Jika data tidak ditemukan
            return '<p>Data tidak ditemukan.</p>';
        }
    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_data_peternakan_sapi', 'edit_data_peternakan_sapi');


function tambah_data_peternakan_sapi() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $ppkbd_database = 'websitekecamatanbintantimur';
    $table_name = $ppkbd_database . '.data_peternakan_sapi';

    if (isset($_POST['submit'])) {
        $nama_pemilik_ternak = sanitize_text_field($_POST['nama_pemilik_ternak']);
                $jenis_ternak = sanitize_text_field($_POST['jenis_ternak']);
                $jumlah_ekor = intval($_POST['jumlah_ekor']);
                $alamat_peternak = sanitize_text_field($_POST['alamat_peternak']);
                $ket = sanitize_text_field($_POST['ket']);

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_peternakan_sapi',
            array(
                'nomor' => $new_nomor,
                'nama_pemilik_ternak' => $nama_pemilik_ternak,
                        'jenis_ternak' => $jenis_ternak,
                        'jumlah_ekor' => $jumlah_ekor,
                        'alamat_peternak' => $alamat_peternak,
                        'ket'=> $ket,
            ),
            array('%d', '%s', '%s', '%d', '%s', '%s')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/data-potensi/";
        </script>';
        exit;
    }

    return '
        <form method="post">
        <label for="uraian">Nama Pemilik Ternak:</label>
                    <input type="text" name="nama_pemilik_ternak" value="' . esc_attr($result->nama_pemilik_ternak) . '"><br>
                    <label for="uraian">Jenis Ternak:</label>
                    <input type="text" name="jenis_ternak" value="' . esc_attr($result->jenis_ternak) . '"><br>
                    <label for="uraian">Jumlah Ekor:</label>
                    <input type="number" name="jumlah_ekor" value="' . esc_attr($result->jumlah_ekor) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Alamat Peternak:</label>
                    <input type="text" name="alamat_peternak" value="' . esc_attr($result->alamat_peternak) . '"><br>
                    <label for="uraian">Keterangan:</label>
                    <input type="text" name="ket" value="' . esc_attr($result->ket) . '"><br>
         <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_peternakan_sapi', 'tambah_data_peternakan_sapi');


function   data_peternakan_kambing() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_peternakan_kambing';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
            text-align: left;
        }
        .custom-table th{
            font-size: 10px;
        }    
        .custom-table td{
            font-size: 10px;
        }  
        .custom-table th, .custom-table td {
            padding: 12px;
        }
        .custom-table thead {
            background-color: #f2f2f2;
            color: #333;
        }
        .custom-table tr {
            border-bottom: 1px solid #ddd;
        }
        /* Media Query untuk layar kecil */
        @media (max-width: 768px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font */
            }
        }
        @media (max-width: 480px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font lebih kecil */
            }
        }
        </style>';

        $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
        $output .= '<table class="custom-table">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Nama Pemilik Ternak</th>';
        $output .= '<th style="padding:12px;">Jenis Ternak</th>';
        $output .= '<th style="padding:12px;">Jumlah (Ekor)</th>';
        $output .= '<th style="padding:12px;">Alamat Peternakan</th>';
        $output .= '<th style="padding:12px;">Ket</th>';
                                   // Tambahkan kolom "Aksi" jika admin login
                                   if (current_user_can('administrator')) {
                                    $output .= '<th style="padding:12px;">Aksi</th>';
                                }
        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->nama_pemilik_ternak) . '</td>';
            $output .= '<td style="padding:12px;">' . format_text($row->jenis_ternak) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->jumlah_ekor) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->alamat_peternak) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->ket) . '</td>';
                                                // Jika admin login, tampilkan tombol Edit
                                                if (current_user_can('administrator')) {
                                                    $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-data-peternakan-kambing?id=' . $row->id . '">Edit</a></td>';
                                    
                                                }
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }
    if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-data-peternakan-kambing" class="tambah-button">Tambah Data Peternakan Kambing</a>';
        $output .= '</div>';
    }
    return $output;
}

function edit_data_peternakan_kambing() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $ppkbd_database = 'websitekecamatanbintantimur';
    $table_name = $ppkbd_database . '.data_peternakan_kambing'; // ppkbd tabel yang benar

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = sanitize_text_field($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_peternakan_kambing',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/data-potensi/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
            // Jika form disubmit untuk update
            if (isset($_POST['submit'])) {
                // Validasi dan sanitasi input dari form
                $nama_pemilik_ternak = sanitize_text_field($_POST['nama_pemilik_ternak']);
                $jenis_ternak = sanitize_text_field($_POST['jenis_ternak']);
                $jumlah_ekor = intval($_POST['jumlah_ekor']);
                $alamat_peternak = sanitize_text_field($_POST['alamat_peternak']);
                $ket = sanitize_text_field($_POST['ket']);

                // Update data ke database
                $wpdb->update(
                    'data_peternakan_kambing',
                    array(
                        'nama_pemilik_ternak' => $nama_pemilik_ternak,
                        'jenis_ternak' => $jenis_ternak,
                        'jumlah_ekor' => $jumlah_ekor,
                        'alamat_peternak' => $alamat_peternak,
                        'ket'=> $ket,
                    ),
                    array('id' => $id),  // Syarat untuk update berdasarkan ID
                    array('%s', '%s', '%d', '%s', '%s'),  // Format tipe data
                    array('%d')  // Format untuk ID
                );

                // Redirect setelah data berhasil diupdate
                echo '<script type="text/javascript">
                    alert("Data berhasil diupdate.");
                    window.location.href = "http://websitekecamatanbintantimur.test/data-potensi/";
                </script>';
                exit;
            }

            // Tampilkan form untuk mengedit data
            return '
                <form method="post">
                    <label for="uraian">Nama Pemilik Ternak:</label>
                    <input type="text" name="nama_pemilik_ternak" value="' . esc_attr($result->nama_pemilik_ternak) . '"><br>
                    <label for="uraian">Jenis Ternak:</label>
                    <input type="text" name="jenis_ternak" value="' . esc_attr($result->jenis_ternak) . '"><br>
                    <label for="uraian">Jumlah Ekor:</label>
                    <input type="number" name="jumlah_ekor" value="' . esc_attr($result->jumlah_ekor) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Alamat Peternak:</label>
                    <input type="text" name="alamat_peternak" value="' . esc_attr($result->alamat_peternak) . '"><br>
                    <label for="uraian">Keterangan:</label>
                    <input type="text" name="ket" value="' . esc_attr($result->ket) . '"><br>
                    <input type="submit" name="submit" value="Update Data" class="update-button">
                    <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
                </form>';
        } else {
            // Jika data tidak ditemukan
            return '<p>Data tidak ditemukan.</p>';
        }
    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_data_peternakan_kambing', 'edit_data_peternakan_kambing');


function tambah_data_peternakan_kambing() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $ppkbd_database = 'websitekecamatanbintantimur';
    $table_name = $ppkbd_database . '.data_peternakan_kambing';

    if (isset($_POST['submit'])) {
        $nama_pemilik_ternak = sanitize_text_field($_POST['nama_pemilik_ternak']);
                $jenis_ternak = sanitize_text_field($_POST['jenis_ternak']);
                $jumlah_ekor = intval($_POST['jumlah_ekor']);
                $alamat_peternak = sanitize_text_field($_POST['alamat_peternak']);
                $ket = sanitize_text_field($_POST['ket']);

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_peternakan_kambing',
            array(
                'nomor' => $new_nomor,
                'nama_pemilik_ternak' => $nama_pemilik_ternak,
                        'jenis_ternak' => $jenis_ternak,
                        'jumlah_ekor' => $jumlah_ekor,
                        'alamat_peternak' => $alamat_peternak,
                        'ket'=> $ket,
            ),
            array('%d', '%s', '%s', '%d', '%s', '%s')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/data-potensi/";
        </script>';
        exit;
    }

    return '
        <form method="post">
        <label for="uraian">Nama Pemilik Ternak:</label>
                    <input type="text" name="nama_pemilik_ternak" value="' . esc_attr($result->nama_pemilik_ternak) . '"><br>
                    <label for="uraian">Jenis Ternak:</label>
                    <input type="text" name="jenis_ternak" value="' . esc_attr($result->jenis_ternak) . '"><br>
                    <label for="uraian">Jumlah Ekor:</label>
                    <input type="number" name="jumlah_ekor" value="' . esc_attr($result->jumlah_ekor) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Alamat Peternak:</label>
                    <input type="text" name="alamat_peternak" value="' . esc_attr($result->alamat_peternak) . '"><br>
                    <label for="uraian">Keterangan:</label>
                    <input type="text" name="ket" value="' . esc_attr($result->ket) . '"><br>
         <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_peternakan_kambing', 'tambah_data_peternakan_kambing');


function   data_peternakan_babi() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_peternakan_babi';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
            text-align: left;
        }
        .custom-table th{
            font-size: 10px;
        }    
        .custom-table td{
            font-size: 10px;
        }  
        .custom-table th, .custom-table td {
            padding: 12px;
        }
        .custom-table thead {
            background-color: #f2f2f2;
            color: #333;
        }
        .custom-table tr {
            border-bottom: 1px solid #ddd;
        }
        /* Media Query untuk layar kecil */
        @media (max-width: 768px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font */
            }
        }
        @media (max-width: 480px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font lebih kecil */
            }
        }
        </style>';

        $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
        $output .= '<table class="custom-table">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Nama Pemilik Ternak</th>';
        $output .= '<th style="padding:12px;">Jenis Ternak</th>';
        $output .= '<th style="padding:12px;">Jumlah (Ekor)</th>';
        $output .= '<th style="padding:12px;">Alamat Peternakan</th>';
        $output .= '<th style="padding:12px;">Ket</th>';
                                   // Tambahkan kolom "Aksi" jika admin login
                            if (current_user_can('administrator')) {
                                $output .= '<th style="padding:12px;">Aksi</th>';
                            }
        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->nama_pemilik_ternak) . '</td>';
            $output .= '<td style="padding:12px;">' . format_text($row->jenis_ternak) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->jumlah_ekor) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->alamat_peternak) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->ket) . '</td>';
                                                // Jika admin login, tampilkan tombol Edit
                                                if (current_user_can('administrator')) {
                                                    $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-data-peternakan-babi?id=' . $row->id . '">Edit</a></td>';
                                    
                                                }
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }
    if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-data-peternakan-babi" class="tambah-button">Tambah Data Peternakan Babi</a>';
        $output .= '</div>';
    }

    return $output;
}

function edit_data_peternakan_babi() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $ppkbd_database = 'websitekecamatanbintantimur';
    $table_name = $ppkbd_database . '.data_peternakan_babi'; // ppkbd tabel yang benar

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = sanitize_text_field($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_peternakan_babi',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/data-potensi/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
            // Jika form disubmit untuk update
            if (isset($_POST['submit'])) {
                // Validasi dan sanitasi input dari form
                $nama_pemilik_ternak = sanitize_text_field($_POST['nama_pemilik_ternak']);
                $jenis_ternak = sanitize_text_field($_POST['jenis_ternak']);
                $jumlah_ekor = intval($_POST['jumlah_ekor']);
                $alamat_peternak = sanitize_text_field($_POST['alamat_peternak']);
                $ket = sanitize_text_field($_POST['ket']);

                // Update data ke database
                $wpdb->update(
                    'data_peternakan_babi',
                    array(
                        'nama_pemilik_ternak' => $nama_pemilik_ternak,
                        'jenis_ternak' => $jenis_ternak,
                        'jumlah_ekor' => $jumlah_ekor,
                        'alamat_peternak' => $alamat_peternak,
                        'ket'=> $ket,
                    ),
                    array('id' => $id),  // Syarat untuk update berdasarkan ID
                    array('%s', '%s', '%d', '%s', '%s'),  // Format tipe data
                    array('%d')  // Format untuk ID
                );

                // Redirect setelah data berhasil diupdate
                echo '<script type="text/javascript">
                    alert("Data berhasil diupdate.");
                    window.location.href = "http://websitekecamatanbintantimur.test/data-potensi/";
                </script>';
                exit;
            }

            // Tampilkan form untuk mengedit data
            return '
                <form method="post">
                    <label for="uraian">Nama Pemilik Ternak:</label>
                    <input type="text" name="nama_pemilik_ternak" value="' . esc_attr($result->nama_pemilik_ternak) . '"><br>
                    <label for="uraian">Jenis Ternak:</label>
                    <input type="text" name="jenis_ternak" value="' . esc_attr($result->jenis_ternak) . '"><br>
                    <label for="uraian">Jumlah Ekor:</label>
                    <input type="number" name="jumlah_ekor" value="' . esc_attr($result->jumlah_ekor) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Alamat Peternak:</label>
                    <input type="text" name="alamat_peternak" value="' . esc_attr($result->alamat_peternak) . '"><br>
                    <label for="uraian">Keterangan:</label>
                    <input type="text" name="ket" value="' . esc_attr($result->ket) . '"><br>
                    <input type="submit" name="submit" value="Update Data" class="update-button">
                    <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
                </form>';
        } else {
            // Jika data tidak ditemukan
            return '<p>Data tidak ditemukan.</p>';
        }
    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_data_peternakan_babi', 'edit_data_peternakan_babi');


function tambah_data_peternakan_babi() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $ppkbd_database = 'websitekecamatanbintantimur';
    $table_name = $ppkbd_database . '.data_peternakan_babi';

    if (isset($_POST['submit'])) {
        $nama_pemilik_ternak = sanitize_text_field($_POST['nama_pemilik_ternak']);
                $jenis_ternak = sanitize_text_field($_POST['jenis_ternak']);
                $jumlah_ekor = intval($_POST['jumlah_ekor']);
                $alamat_peternak = sanitize_text_field($_POST['alamat_peternak']);
                $ket = sanitize_text_field($_POST['ket']);

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_peternakan_babi',
            array(
                'nomor' => $new_nomor,
                'nama_pemilik_ternak' => $nama_pemilik_ternak,
                        'jenis_ternak' => $jenis_ternak,
                        'jumlah_ekor' => $jumlah_ekor,
                        'alamat_peternak' => $alamat_peternak,
                        'ket'=> $ket,
            ),
            array('%d', '%s', '%s', '%d', '%s', '%s')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/data-potensi/";
        </script>';
        exit;
    }

    return '
        <form method="post">
        <label for="uraian">Nama Pemilik Ternak:</label>
                    <input type="text" name="nama_pemilik_ternak" value="' . esc_attr($result->nama_pemilik_ternak) . '"><br>
                    <label for="uraian">Jenis Ternak:</label>
                    <input type="text" name="jenis_ternak" value="' . esc_attr($result->jenis_ternak) . '"><br>
                    <label for="uraian">Jumlah Ekor:</label>
                    <input type="number" name="jumlah_ekor" value="' . esc_attr($result->jumlah_ekor) . '" inputmode="numeric" pattern="\d*"><br>
                    <label for="uraian">Alamat Peternak:</label>
                    <input type="text" name="alamat_peternak" value="' . esc_attr($result->alamat_peternak) . '"><br>
                    <label for="uraian">Keterangan:</label>
                    <input type="text" name="ket" value="' . esc_attr($result->ket) . '"><br>
         <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_peternakan_babi', 'tambah_data_peternakan_babi');


function   data_luas_kawasan_hutan() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_luas_kawasan_hutan';

    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Mulai menampilkan tabel
    if (!empty($results)) {
        $output = '<style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
            text-align: left;
        }
        .custom-table th{
            font-size: 10px;
        }    
        .custom-table td{
            font-size: 10px;
        }  
        .custom-table th, .custom-table td {
            padding: 12px;
        }
        .custom-table thead {
            background-color: #f2f2f2;
            color: #333;
        }
        .custom-table tr {
            border-bottom: 1px solid #ddd;
        }
        /* Media Query untuk layar kecil */
        @media (max-width: 768px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font */
            }
        }
        @media (max-width: 480px) {
            .custom-table {
                font-size: 10px; /* Mengubah ukuran font lebih kecil */
            }
        }
        </style>';

        $output .= '<div style="overflow-x:auto;">'; // Membuat tabel dapat digeser
        $output .= '<table class="custom-table">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th style="padding:12px;">No.</th>';
        $output .= '<th style="padding:12px;">Jenis Hutan</th>';
        $output .= '<th style="padding:12px;">Luas (Ha)</th>';
        $output .= '<th style="padding:12px;">Ket</th>';
                                    // Tambahkan kolom "Aksi" jika admin login
                            if (current_user_can('administrator')) {
                                $output .= '<th style="padding:12px;">Aksi</th>';
                            }
        $output .= '</tr></thead><tbody>';
        
        foreach ($results as $row) {
            $output .= '<tr style="border-bottom:1px solid #ddd;">';
            $output .= '<td style="padding:12px;">' . esc_html($row->nomor) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->jenis_hutan) . '</td>';
            $output .= '<td style="padding:12px;">' . format_text($row->luas) . '</td>';
            $output .= '<td style="padding:12px;">' . esc_html($row->ket) . '</td>';
                                    // Jika admin login, tampilkan tombol Edit
                                    if (current_user_can('administrator')) {
                                        $output .= '<td style="padding:12px;"><a href="http://websitekecamatanbintantimur.test/edit-data-luas-kawasan-hutan?id=' . $row->id . '">Edit</a></td>';
                        
                                    }
            $output .= '</tr>';
        }
        $output .= '</tbody></table>';
        $output .= '</div>';
    } else {
        $output = '<p>Tidak ada data yang ditemukan.</p>';
    }
    if (current_user_can('administrator')) {
        $output .= '<div style="margin-top: 20px;">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-data-luas-kawasan-hutan" class="tambah-button">Tambah Data Luas Kawasan Hutan</a>';
        $output .= '</div>';
    }
    return $output;
}

function edit_data_luas_kawasan_hutan() {
    // Periksa apakah pengguna adalah administrator
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk mengedit data.';
    }

    global $wpdb;
    $ppkbd_database = 'websitekecamatanbintantimur';
    $table_name = $ppkbd_database . '.data_luas_kawasan_hutan'; // ppkbd tabel yang benar

    // Ambil ID dari URL
    if (isset($_GET['id'])) {
        $id = sanitize_text_field($_GET['id']);

        // Cek jika ada permintaan untuk menghapus data
        if (isset($_POST['delete'])) {
            // Hapus data dari database
            $wpdb->delete(
                'data_luas_kawasan_hutan',
                array('id' => $id),
                array('%d')
            );

            // Redirect setelah data berhasil dihapus
            echo '<script type="text/javascript">
                alert("Data berhasil dihapus.");
                window.location.href = "http://websitekecamatanbintantimur.test/data-potensi/";
            </script>';
            exit;
        }

        // Ambil data berdasarkan ID
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Jika data ditemukan
        if ($result) {
            // Jika form disubmit untuk update
            if (isset($_POST['submit'])) {
                // Validasi dan sanitasi input dari form
                $jenis_hutan = sanitize_text_field($_POST['jenis_hutan']);
                $luas = floatval($_POST['luas']);
                $ket = sanitize_text_field($_POST['ket']);

                // Update data ke database
                $wpdb->update(
                    'data_luas_kawasan_hutan',
                    array(
                        'jenis_hutan' => $jenis_hutan,
                        'luas' => $luas,
                        'ket'=> $ket,
                    ),
                    array('id' => $id),  // Syarat untuk update berdasarkan ID
                    array('%s', '%.2f', '%s'),  // Format tipe data
                    array('%d')  // Format untuk ID
                );

                // Redirect setelah data berhasil diupdate
                echo '<script type="text/javascript">
                    alert("Data berhasil diupdate.");
                    window.location.href = "http://websitekecamatanbintantimur.test/data-potensi/";
                </script>';
                exit;
            }

            // Tampilkan form untuk mengedit data
            return '
                <form method="post">
                    <label for="uraian">Jenis Hutan:</label>
                    <input type="text" name="jenis_hutan" value="' . esc_attr($result->jenis_hutan) . '"><br>
                    <label for="uraian">Luas:</label>
                    <input type="number" name="luas" value="' . esc_attr($result->luas) . '" step="0.01" inputmode="decimal" pattern="[0-9]+(\.[0-9]{1,2})?" /><br>
                    <label for="uraian">Keterangan:</label>
                    <input type="text" name="ket" value="' . esc_attr($result->ket) . '"><br>
                    <input type="submit" name="submit" value="Update Data" class="update-button">
                    <input type="submit" name="delete" value="Hapus Data" class="delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">
                </form>';
        } else {
            // Jika data tidak ditemukan
            return '<p>Data tidak ditemukan.</p>';
        }
    } else {
        // Jika ID tidak ditemukan di URL
        return '<p>ID tidak ditemukan.</p>';
    }
}
add_shortcode('edit_data_luas_kawasan_hutan', 'edit_data_luas_kawasan_hutan');


function tambah_data_luas_kawasan_hutan() {
    if (!current_user_can('administrator')) {
        return 'Anda tidak memiliki akses untuk menambah data.';
    }

    global $wpdb;
    $ppkbd_database = 'websitekecamatanbintantimur';
    $table_name = $ppkbd_database . '.data_luas_kawasan_hutan';

    if (isset($_POST['submit'])) {
        $jenis_hutan = sanitize_text_field($_POST['jenis_hutan']);
                $luas = floatval($_POST['luas']);
                $ket = sanitize_text_field($_POST['ket']);

        // Mendapatkan nilai nomor terakhir dari database
        $last_nomor = $wpdb->get_var("SELECT MAX(nomor) FROM $table_name");
        $new_nomor = $last_nomor ? $last_nomor + 1 : 1; // Jika tidak ada nilai, mulai dari 1

        // Menyisipkan data ke dalam tabel
        $wpdb->insert(
            'data_luas_kawasan_hutan',
            array(
                'nomor' => $new_nomor,
                'jenis_hutan' => $jenis_hutan,
                        'luas' => $luas,
                        'ket'=> $ket,
            ),
            array('%d', '%s', '%.2f', '%s')
        );

        // Redirect setelah berhasil menambah data
        echo '<script type="text/javascript">
            alert("Data berhasil ditambahkan.");
            window.location.href = "http://websitekecamatanbintantimur.test/data-potensi/";
        </script>';
        exit;
    }

    return '
        <form method="post">
        <label for="uraian">Jenis Hutan:</label>
                    <input type="text" name="jenis_hutan" value="' . esc_attr($result->jenis_hutan) . '"><br>
                    <label for="uraian">Luas:</label>
                    <input type="number" name="luas" value="' . esc_attr($result->luas) . '" step="0.01" inputmode="decimal" pattern="[0-9]+(\.[0-9]{1,2})?" /><br>
                    <label for="uraian">Keterangan:</label>
                    <input type="text" name="ket" value="' . esc_attr($result->ket) . '"><br>
         <input type="submit" name="submit" value="Tambah Data" class="tambah-button">
        </form>';
}
add_shortcode('tambah_data_luas_kawasan_hutan', 'tambah_data_luas_kawasan_hutan');


// Tambahkan Shortcode agar bisa digunakan di halaman atau posting WordPress
add_shortcode('data_luas_wilayah_kelurahan', 'data_luas_wilayah_kelurahan');
add_shortcode('data_jarak_tempuh', 'data_jarak_tempuh');
add_shortcode('data_kondisi_geografis', 'data_kondisi_geografis');
add_shortcode('data_aparatur_kecamatan', 'data_aparatur_kecamatan');
add_shortcode('data_aparatur_keluruhan_kijang_kota', 'data_aparatur_keluruhan_kijang_kota');
add_shortcode('data_aparatur_keluruhan_sungai_enam', 'data_aparatur_keluruhan_sungai_enam');
add_shortcode('data_aparatur_keluruhan_gunung_lengkuas', 'data_aparatur_keluruhan_gunung_lengkuas');
add_shortcode('data_aparatur_keluruhan_sungai_lekop', 'data_aparatur_keluruhan_sungai_lekop');
add_shortcode('data_kepengurusan_pkk_kecamatan', 'data_kepengurusan_pkk_kecamatan');
add_shortcode('data_pokja_1', 'data_pokja_1');
add_shortcode('data_pokja_2', 'data_pokja_2');
add_shortcode('data_pokja_3', 'data_pokja_3');
add_shortcode('data_pokja_4', 'data_pokja_4');
add_shortcode('data_lembaga_organisasi_kemasyarakatan_rt_rw', 'data_lembaga_organisasi_kemasyarakatan_rt_rw');
add_shortcode('data_lpm_kelurahan_kijang_kota', 'data_lpm_kelurahan_kijang_kota');
add_shortcode('data_lpm_kelurahan_sungai_enam', 'data_lpm_kelurahan_sungai_enam');
add_shortcode('data_lpm_kelurahan_gunung_lengkuas', 'data_lpm_kelurahan_gunung_lengkuas');
add_shortcode('data_lpm_kelurahan_sungai_lekop', 'data_lpm_kelurahan_sungai_lekop');
add_shortcode('data_daerah_pemilihan_kabupaten_bintan', 'data_daerah_pemilihan_kabupaten_bintan');
add_shortcode('data_partai_politik_kecamatan_bintan_timur', 'data_partai_politik_kecamatan_bintan_timur');
add_shortcode('data_tps', 'data_tps');
add_shortcode('data_pemilih_tetap', 'data_pemilih_tetap');
add_shortcode('data_pemilih_disabilitas', 'data_pemilih_disabilitas');
add_shortcode('data_penduduk_wni', 'data_penduduk_wni');
add_shortcode('data_kepemilikan_kk', 'data_kepemilikan_kk');
add_shortcode('data_penduduk_berdasarkan_agama', 'data_penduduk_berdasarkan_agama');
add_shortcode('data_penduduk_berdasarkan_pendidikan', 'data_penduduk_berdasarkan_pendidikan');
add_shortcode('data_penduduk_berdasarkan_umur', 'data_penduduk_berdasarkan_umur');
add_shortcode('data_penduduk_berdasarkan_kawin', 'data_penduduk_berdasarkan_kawin');
add_shortcode('data_penduduk_berdasarkan_produktif', 'data_penduduk_berdasarkan_produktif');
add_shortcode('data_penduduk_berdasarkan_cacat_mental', 'data_penduduk_berdasarkan_cacat_mental');
add_shortcode('data_jumlah_murid_tpq_tk_paud_sd_mi_smp_mts', 'data_jumlah_murid_tpq_tk_paud_sd_mi_smp_mts');
add_shortcode('data_jumlah_murid_sma_1_bintan_kelas_x', 'data_jumlah_murid_sma_1_bintan_kelas_x');
add_shortcode('data_jumlah_murid_sma_1_bintan_kelas_xi', 'data_jumlah_murid_sma_1_bintan_kelas_xi');
add_shortcode('data_jumlah_murid_sma_1_bintan_kelas_xii', 'data_jumlah_murid_sma_1_bintan_kelas_xii');
add_shortcode('data_jumlah_murid_smk_1_bintan_kelas_x', 'data_jumlah_murid_smk_1_bintan_kelas_x');
add_shortcode('data_jumlah_murid_smk_1_bintan_kelas_xi', 'data_jumlah_murid_smk_1_bintan_kelas_xi');
add_shortcode('data_jumlah_murid_smk_1_bintan_kelas_xii', 'data_jumlah_murid_smk_1_bintan_kelas_xii');
add_shortcode('data_jumlah_murid_man_bintan_kelas_x', 'data_jumlah_murid_man_bintan_kelas_x');
add_shortcode('data_jumlah_murid_man_bintan_kelas_xi', 'data_jumlah_murid_man_bintan_kelas_xi');
add_shortcode('data_jumlah_murid_man_bintan_kelas_xii', 'data_jumlah_murid_man_bintan_kelas_xii');
add_shortcode('data_jumlah_pengajar_tk_sd_mi_smp_mts', 'data_jumlah_pengajar_tk_sd_mi_smp_mts');
add_shortcode('data_jumlah_pengajar_sma_smk_man', 'data_jumlah_pengajar_sma_smk_man');
add_shortcode('data_relasi_pembangunan_kelurahan_kijang_kota', 'data_relasi_pembangunan_kelurahan_kijang_kota');
add_shortcode('data_relasi_pembangunan_kelurahan_sungai_enam', 'data_relasi_pembangunan_kelurahan_sungai_enam');
add_shortcode('data_relasi_pembangunan_kelurahan_gunung_lengkuas', 'data_relasi_pembangunan_kelurahan_gunung_lengkuas');
add_shortcode('data_relasi_pembangunan_kelurahan_sungai_lekop', 'data_relasi_pembangunan_kelurahan_sungai_lekop');
add_shortcode('data_penyuluh_sosial_masyarakat', 'data_penyuluh_sosial_masyarakat');
add_shortcode('data_pendamping_lansia', 'data_pendamping_lansia');
add_shortcode('data_tpk_kelurahan_kijang_kota', 'data_tpk_kelurahan_kijang_kota');
add_shortcode('data_tpk_kelurahan_sungai_enam', 'data_tpk_kelurahan_sungai_enam');
add_shortcode('data_tpk_kelurahan_gunung_lengkuas', 'data_tpk_kelurahan_gunung_lengkuas');
add_shortcode('data_tpk_kelurahan_sungai_lekop', 'data_tpk_kelurahan_sungai_lekop');
add_shortcode('data_ppkbd', 'data_ppkbd');
add_shortcode('data_posyandu_menurut_strata', 'data_posyandu_menurut_strata');
add_shortcode('data_tenaga_medis', 'data_tenaga_medis');
add_shortcode('data_kasus_demam_berdarah', 'data_kasus_demam_berdarah');
add_shortcode('data_kasus_malaria', 'data_kasus_malaria');
add_shortcode('data_sarana_ibadah', 'data_sarana_ibadah');
add_shortcode('data_nikah_cerai_rujuk_talak', 'data_nikah_cerai_rujuk_talak');
add_shortcode('data_sebaran_kesenian_bintan_timur', 'data_sebaran_kesenian_bintan_timur');
add_shortcode('data_pengamanan_pertahanan', 'data_pengamanan_pertahanan');
add_shortcode('data_kenakalan_remaja', 'data_kenakalan_remaja');
add_shortcode('data_pelanggaran_hukum', 'data_pelanggaran_hukum');
add_shortcode('data_bencana_alam', 'data_bencana_alam');
add_shortcode('data_usaha_kube_kijang_kota', 'data_usaha_kube_kijang_kota');
add_shortcode('data_usaha_kube_sungai_enam_dan_gunung_lengkuas', 'data_usaha_kube_sungai_enam_dan_gunung_lengkuas');
add_shortcode('data_usaha_kube_sungai_lekop', 'data_usaha_kube_sungai_lekop');
add_shortcode('data_ikm', 'data_ikm');
add_shortcode('data_kelompok_tani_kelurahan_kijang_kota', 'data_kelompok_tani_kelurahan_kijang_kota');
add_shortcode('data_kelompok_tani_kelurahan_sungai_enam', 'data_kelompok_tani_kelurahan_sungai_enam');
add_shortcode('data_kelompok_tani_kelurahan_gunung_lengkuas', 'data_kelompok_tani_kelurahan_gunung_lengkuas');
add_shortcode('data_kelompok_tani_kelurahan_sungai_lekop', 'data_kelompok_tani_kelurahan_sungai_lekop');
add_shortcode('data_luas_lahan_pertanian_dan_perkebunan', 'data_luas_lahan_pertanian_dan_perkebunan');
add_shortcode('data_tanaman_hortikultura', 'data_tanaman_hortikultura');
add_shortcode('data_nelayan_penerima_bantuan_kelurahan_kijang_kota', 'data_nelayan_penerima_bantuan_kelurahan_kijang_kota');
add_shortcode('data_nelayan_penerima_bantuan_kelurahan_sungai_enam', 'data_nelayan_penerima_bantuan_kelurahan_sungai_enam');
add_shortcode('data_nelayan_penerima_bantuan_kelurahan_sungai_lekop', 'data_nelayan_penerima_bantuan_kelurahan_sungai_lekop');
add_shortcode('data_peternakan_ayam_buras', 'data_peternakan_ayam_buras');
add_shortcode('data_peternakan_ayam_broiler', 'data_peternakan_ayam_broiler');
add_shortcode('data_peternakan_itik', 'data_peternakan_itik');
add_shortcode('data_peternakan_sapi', 'data_peternakan_sapi');
add_shortcode('data_peternakan_kambing', 'data_peternakan_kambing');
add_shortcode('data_peternakan_babi', 'data_peternakan_babi');
add_shortcode('data_luas_kawasan_hutan', 'data_luas_kawasan_hutan');



function tampilkan_berita_desa() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_berita';
    
    // Query ke database
    $results = $wpdb->get_results("SELECT * FROM $table_name");
    
    // Dapatkan URL tema WordPress
    $theme_directory_uri = get_stylesheet_directory_uri();
    
    // HTML untuk menampilkan list berita dengan gambar
    $output = '<div class="berita-container">';
    if ($results) {
        foreach ($results as $item) {
            $gambar_url = $theme_directory_uri . '/assets/img/' . esc_html($item->gambar);
            $judul_berita = urlencode($item->judul_berita); // Encode judul untuk parameter URL
            
            $output .= '<div class="berita-card">';
            $output .= '<div class="berita-image">';
            $output .= '<img src="' . $gambar_url . '" alt="' . esc_html($item->judul_berita) . '">';
            $output .= '</div>';
            $output .= '<div class="berita-content">';
            $output .= '<h3 class="berita-judul">' . esc_html($item->judul_berita) . '</h3>';
            $output .= '<p class="berita-deskripsi">' . esc_html($item->isi_berita) . '</p>';
            $output .= '<a href="http://websitekecamatanbintantimur.test/detail-berita/?judul=' . $judul_berita . '" class="berita-link">Baca Selengkapnya</a>';
            $output .= '</div>';
            $output .= '</div>';
        }
    } else {
        $output .= '<p>Tidak ada berita yang ditemukan.</p>';
    }
    $output .= '</div>';
    
    if (is_user_logged_in()) {
        $output .= '<div class="upload-berita-button">';
        $output .= '<a href="http://websitekecamatanbintantimur.test/tambah-berita/" class="btn-upload-berita">Upload Berita</a>';
        $output .= '</div>';
    }
    
    return $output;
}


add_shortcode('berita_desa', 'tampilkan_berita_desa');

// Fungsi untuk menampilkan form berita
function berita_form_shortcode() {
    ob_start(); // Mulai output buffering
    ?>
    <div class="berita-form-container">
        <form id="beritaForm" method="post" enctype="multipart/form-data" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <input type="hidden" name="action" value="proses_berita_form">
            <div class="form-group">
                <label for="judul_berita">Judul Berita</label>
                <input type="text" id="judul_berita" name="judul_berita" required>
            </div>
            <div class="form-group">
                <label for="isi_berita">Isi Berita</label>
                <textarea id="isi_berita" name="isi_berita" rows="5" required></textarea>
            </div>
            <div class="form-group">
                <label for="penulis">Penulis</label>
                <input type="text" id="penulis" name="penulis" required>
            </div>
            <div class="form-group">
                <label for="gambar">Gambar</label>
                <input type="file" id="gambar" name="gambar" accept="image/*">
            </div>
            <div class="form-group">
                <input type="submit" name="submit_berita" value="Buat Berita">
            </div>
        </form>
        <div id="form-message">
            <?php
            if (isset($_GET['status'])) {
                if ($_GET['status'] === 'success') {
                    echo '<p>Berita berhasil dibuat!</p>';
                } elseif ($_GET['status'] === 'error') {
                    echo '<p>Terjadi kesalahan. Silakan coba lagi.</p>';
                }
            }
            ?>
        </div>
    </div>
    <style>
        .berita-form-container {
            width: 80%;
            max-width: 600px;
            margin: auto;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input, .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .form-group input[type="submit"] {
            background: #0073aa;
            color: #fff;
            border: none;
            cursor: pointer;
            padding: 10px 15px;
        }
        .form-group input[type="submit"]:hover {
            background: #005a87;
        }
    </style>
    <?php
    return ob_get_clean(); // Kembalikan output buffer dan hentikan buffering
}

// Daftarkan shortcode
add_shortcode('berita_form', 'berita_form_shortcode');

// Fungsi untuk memproses data form
function proses_berita_form() {
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $wpdb->$nama_database . 'data_berita'; // Ganti dengan tabel yang sesuai dengan prefix

    // Cek apakah form dikirim
    if (isset($_POST['submit_berita'])) {
        $judul_berita = sanitize_text_field($_POST['judul_berita']);
        $isi_berita = wp_kses_post($_POST['isi_berita']);
        $penulis = sanitize_text_field($_POST['penulis']);

        // Validasi panjang teks
        if (strlen($judul_berita) > 50) {
            wp_redirect(add_query_arg('status', 'error', wp_get_referer()));
            exit;
        }
        if (str_word_count($isi_berita) > 250) {
            wp_redirect(add_query_arg('status', 'error', wp_get_referer()));
            exit;
        }
        if (strlen($penulis) > 50) {
            wp_redirect(add_query_arg('status', 'error', wp_get_referer()));
            exit;
        }

        // Proses gambar
        $gambar_name = '';
        if (!empty($_FILES['gambar']['name'])) {
            $uploaded_file = $_FILES['gambar'];
            $upload_dir = get_stylesheet_directory() . '/assets/img/';
            $upload_file = $upload_dir . basename($uploaded_file['name']);

            // Validasi ukuran gambar
            if ($uploaded_file['size'] > 10 * 1024 * 1024) { // 10 MB
                wp_redirect(add_query_arg('status', 'error', wp_get_referer()));
                exit;
            }

            // Validasi tipe file
            $image_file_type = strtolower(pathinfo($upload_file, PATHINFO_EXTENSION));
            $allowed_file_types = array('jpg', 'jpeg', 'png', 'gif');
            if (in_array($image_file_type, $allowed_file_types)) {
                if (move_uploaded_file($uploaded_file['tmp_name'], $upload_file)) {
                    $gambar_name = basename($uploaded_file['name']);
                } else {
                    wp_redirect(add_query_arg('status', 'error', wp_get_referer()));
                    exit;
                }
            } else {
                wp_redirect(add_query_arg('status', 'error', wp_get_referer()));
                exit;
            }
        }

        // Insert data
        $result = $wpdb->insert(
            $table_name,
            array(
                'judul_berita' => $judul_berita,
                'isi_berita' => $isi_berita,
                'penulis' => $penulis,
                'gambar' => $gambar_name,
            ),
            array('%s', '%s', '%s', '%s')
        );

        if ($result === false) {
            wp_redirect(add_query_arg('status', 'error', wp_get_referer()));
        } else {
            wp_redirect(add_query_arg('status', 'success', wp_get_referer()));
        }
        exit;
    }
}

// Daftarkan aksi
add_action('admin_post_proses_berita_form', 'proses_berita_form');
add_action('admin_post_nopriv_proses_berita_form', 'proses_berita_form');

function detail_berita_shortcode() {
    // Ambil parameter 'judul' dari URL
    $judul_berita = isset($_GET['judul']) ? sanitize_text_field($_GET['judul']) : '';

    // Query ke database
    global $wpdb;
    $nama_database = 'websitekecamatanbintantimur';
    $table_name = $nama_database . '.data_berita';

    // Dapatkan detail berita berdasarkan judul
    $query = $wpdb->prepare("SELECT * FROM $table_name WHERE judul_berita = %s", $judul_berita);
    $berita = $wpdb->get_row($query);

    // HTML untuk menampilkan berita
    $output = '<div class="berita-detail">';
    if ($berita) {
        $output .= '<div class="berita-info">';
        $output .= '<h1 class="berita-judul">' . esc_html($berita->judul_berita) . '</h1>';
        $gambar_url = $berita->gambar ? get_stylesheet_directory_uri() . '/assets/img/' . esc_html($berita->gambar) : '';

        $output .= '<div class="berita-content">';
        $output .= '<div class="detail-berita-gambar">';
        if ($gambar_url) {
            $output .= '<img src="' . $gambar_url . '" alt="' . esc_html($berita->judul_berita) . '">';
        }
        $output .= '</div>'; // End of berita-gambar
        $output .= '<p class="berita-penulis"><strong>Penulis:</strong> ' . esc_html($berita->penulis) . '</p>';
        $output .= '<p class="detail-berita-isi">' . esc_html($berita->isi_berita) . '</p>';
        $output .= '</div>'; // End of berita-content
        $output .= '</div>'; // End of berita-info
    } else {
        $output .= '<p>Berita tidak ditemukan.</p>';
    }
    $output .= '</div>'; // End of berita-detail

    return $output;
}
add_shortcode('detail_berita', 'detail_berita_shortcode');



if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Define Constants
 */
define( 'ASTRA_THEME_VERSION', '4.7.3' );
define( 'ASTRA_THEME_SETTINGS', 'astra-settings' );
define( 'ASTRA_THEME_DIR', trailingslashit( get_template_directory() ) );
define( 'ASTRA_THEME_URI', trailingslashit( esc_url( get_template_directory_uri() ) ) );

/**
 * Minimum Version requirement of the Astra Pro addon.
 * This constant will be used to display the notice asking user to update the Astra addon to the version defined below.
 */
define( 'ASTRA_EXT_MIN_VER', '4.7.0' );

/**
 * Setup helper functions of Astra.
 */
require_once ASTRA_THEME_DIR . 'inc/core/class-astra-theme-options.php';
require_once ASTRA_THEME_DIR . 'inc/core/class-theme-strings.php';
require_once ASTRA_THEME_DIR . 'inc/core/common-functions.php';
require_once ASTRA_THEME_DIR . 'inc/core/class-astra-icons.php';

define( 'ASTRA_PRO_UPGRADE_URL', astra_get_pro_url( 'https://wpastra.com/pro/', 'dashboard', 'free-theme', 'upgrade-now' ) );
define( 'ASTRA_PRO_CUSTOMIZER_UPGRADE_URL', astra_get_pro_url( 'https://wpastra.com/pro/', 'customizer', 'free-theme', 'upgrade' ) );

/**
 * Update theme
 */
require_once ASTRA_THEME_DIR . 'inc/theme-update/astra-update-functions.php';
require_once ASTRA_THEME_DIR . 'inc/theme-update/class-astra-theme-background-updater.php';

/**
 * Fonts Files
 */
require_once ASTRA_THEME_DIR . 'inc/customizer/class-astra-font-families.php';
if ( is_admin() ) {
	require_once ASTRA_THEME_DIR . 'inc/customizer/class-astra-fonts-data.php';
}

require_once ASTRA_THEME_DIR . 'inc/lib/webfont/class-astra-webfont-loader.php';
require_once ASTRA_THEME_DIR . 'inc/lib/docs/class-astra-docs-loader.php';
require_once ASTRA_THEME_DIR . 'inc/customizer/class-astra-fonts.php';

require_once ASTRA_THEME_DIR . 'inc/dynamic-css/custom-menu-old-header.php';
require_once ASTRA_THEME_DIR . 'inc/dynamic-css/container-layouts.php';
require_once ASTRA_THEME_DIR . 'inc/dynamic-css/astra-icons.php';
require_once ASTRA_THEME_DIR . 'inc/core/class-astra-walker-page.php';
require_once ASTRA_THEME_DIR . 'inc/core/class-astra-enqueue-scripts.php';
require_once ASTRA_THEME_DIR . 'inc/core/class-gutenberg-editor-css.php';
require_once ASTRA_THEME_DIR . 'inc/core/class-astra-wp-editor-css.php';
require_once ASTRA_THEME_DIR . 'inc/dynamic-css/block-editor-compatibility.php';
require_once ASTRA_THEME_DIR . 'inc/dynamic-css/inline-on-mobile.php';
require_once ASTRA_THEME_DIR . 'inc/dynamic-css/content-background.php';
require_once ASTRA_THEME_DIR . 'inc/class-astra-dynamic-css.php';
require_once ASTRA_THEME_DIR . 'inc/class-astra-global-palette.php';

/**
 * Custom template tags for this theme.
 */
require_once ASTRA_THEME_DIR . 'inc/core/class-astra-attr.php';
require_once ASTRA_THEME_DIR . 'inc/template-tags.php';

require_once ASTRA_THEME_DIR . 'inc/widgets.php';
require_once ASTRA_THEME_DIR . 'inc/core/theme-hooks.php';
require_once ASTRA_THEME_DIR . 'inc/admin-functions.php';
require_once ASTRA_THEME_DIR . 'inc/core/sidebar-manager.php';

/**
 * Markup Functions
 */
require_once ASTRA_THEME_DIR . 'inc/markup-extras.php';
require_once ASTRA_THEME_DIR . 'inc/extras.php';
require_once ASTRA_THEME_DIR . 'inc/blog/blog-config.php';
require_once ASTRA_THEME_DIR . 'inc/blog/blog.php';
require_once ASTRA_THEME_DIR . 'inc/blog/single-blog.php';

/**
 * Markup Files
 */
require_once ASTRA_THEME_DIR . 'inc/template-parts.php';
require_once ASTRA_THEME_DIR . 'inc/class-astra-loop.php';
require_once ASTRA_THEME_DIR . 'inc/class-astra-mobile-header.php';

/**
 * Functions and definitions.
 */
require_once ASTRA_THEME_DIR . 'inc/class-astra-after-setup-theme.php';

// Required files.
require_once ASTRA_THEME_DIR . 'inc/core/class-astra-admin-helper.php';

require_once ASTRA_THEME_DIR . 'inc/schema/class-astra-schema.php';

/* Setup API */
require_once ASTRA_THEME_DIR . 'admin/includes/class-astra-api-init.php';

if ( is_admin() ) {
	/**
	 * Admin Menu Settings
	 */
	require_once ASTRA_THEME_DIR . 'inc/core/class-astra-admin-settings.php';
	require_once ASTRA_THEME_DIR . 'admin/class-astra-admin-loader.php';
	require_once ASTRA_THEME_DIR . 'inc/lib/astra-notices/class-astra-notices.php';
}

/**
 * Metabox additions.
 */
require_once ASTRA_THEME_DIR . 'inc/metabox/class-astra-meta-boxes.php';

require_once ASTRA_THEME_DIR . 'inc/metabox/class-astra-meta-box-operations.php';

/**
 * Customizer additions.
 */
require_once ASTRA_THEME_DIR . 'inc/customizer/class-astra-customizer.php';

/**
 * Astra Modules.
 */
require_once ASTRA_THEME_DIR . 'inc/modules/posts-structures/class-astra-post-structures.php';
require_once ASTRA_THEME_DIR . 'inc/modules/related-posts/class-astra-related-posts.php';

/**
 * Compatibility
 */
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-gutenberg.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-jetpack.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/woocommerce/class-astra-woocommerce.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/edd/class-astra-edd.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/lifterlms/class-astra-lifterlms.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/learndash/class-astra-learndash.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-beaver-builder.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-bb-ultimate-addon.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-contact-form-7.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-visual-composer.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-site-origin.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-gravity-forms.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-bne-flyout.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-ubermeu.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-divi-builder.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-amp.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-yoast-seo.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/surecart/class-astra-surecart.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-starter-content.php';
require_once ASTRA_THEME_DIR . 'inc/addons/transparent-header/class-astra-ext-transparent-header.php';
require_once ASTRA_THEME_DIR . 'inc/addons/breadcrumbs/class-astra-breadcrumbs.php';
require_once ASTRA_THEME_DIR . 'inc/addons/scroll-to-top/class-astra-scroll-to-top.php';
require_once ASTRA_THEME_DIR . 'inc/addons/heading-colors/class-astra-heading-colors.php';
require_once ASTRA_THEME_DIR . 'inc/builder/class-astra-builder-loader.php';

// Elementor Compatibility requires PHP 5.4 for namespaces.
if ( version_compare( PHP_VERSION, '5.4', '>=' ) ) {
	require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-elementor.php';
	require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-elementor-pro.php';
	require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-web-stories.php';
}

// Beaver Themer compatibility requires PHP 5.3 for anonymous functions.
if ( version_compare( PHP_VERSION, '5.3', '>=' ) ) {
	require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-beaver-themer.php';
}

require_once ASTRA_THEME_DIR . 'inc/core/markup/class-astra-markup.php';

/**
 * Load deprecated functions
 */
require_once ASTRA_THEME_DIR . 'inc/core/deprecated/deprecated-filters.php';
require_once ASTRA_THEME_DIR . 'inc/core/deprecated/deprecated-hooks.php';
require_once ASTRA_THEME_DIR . 'inc/core/deprecated/deprecated-functions.php';