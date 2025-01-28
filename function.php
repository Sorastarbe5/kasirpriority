<?php

session_start();

//koneksi
$conn = mysqli_connect('localhost','root','','kasir');

//login
if(isset($_POST['login'])){
    //inisiate variable 
    $username = $_POST['username'];
    $password = $_POST['password'];

    $check = mysqli_query($conn,"SELECT * FROM user WHERE username='$username' and password='$password' ");
    $hitung = mysqli_num_rows($check);

    if($hitung>0){
        //Jika data di temukan
        //berhasil login

        $_SESSION['login'] = 'True';
        header('location:index.php');
    } else {
        //jika tidak ditemukan
        //gagal login
        echo '
        <script>alert("Username atau Password salah");
        window.location.herf="login.php"
        </script>
        ';
    }
}

if(isset($_POST['tambahbarang'])){
    $namaproduk = $_POST['namaproduk'];
    $deskripsi = $_POST['deskripsi'];
    $stock = $_POST['stock'];
    $harga = $_POST['harga'];

    $insert = mysqli_query($conn,"insert into produk (nama_produk,deskripsi,harga,stock) values ('$namaproduk','$deskripsi','$harga','$stock')");

    if($insert){
        header('location:stock.php');
    } else {
        echo '
        <script>alert("Gagal menambah barang baru");
        window.location.herf="stock.php"
        </script>
        ';
    }
};

if(isset($_POST['tambahpelanggan'])){
    $namapelanggan = $_POST['nama_pelanggan'];
    $notelp = $_POST['no_telp'];
    $alamat = $_POST['alamat'];

    // Tambahkan untuk mengecek data yang diterima
    echo "Nama: $namapelanggan, No Telp: $notelp, Alamat: $alamat";

    $insert = mysqli_query($conn,"insert into pelanggan (nama_pelanggan,no_telp,alamat) values ('$namapelanggan','$notelp','$alamat')");

    if($insert){
        header('location:pelanggan.php');
    } else {
        echo '
        <script>alert("Gagal menambah pelanggan baru");
        window.location.herf="pelanggan.php"
        </script>
        ';
    }
}

if(isset($_POST['tambahpesanan'])){
        $idpelanggan = $_POST['id_pelanggan'];
    
        $insert = mysqli_query($conn,"insert into pesanan (id_pelanggan) values ('$idpelanggan')");
    
        if($insert){
            header('location:index.php');
        } else {
            echo '
            <script>alert("Gagal menambah pesanan baru");
            window.location.herf="index.php"
            </script>
            ';
        }
}

if(isset($_POST['addproduk'])){
    $idproduk = $_POST['id_produk'];
    $idp = $_POST['idp']; //idpesanan
    $qty = $_POST['qty'];

    //hitung stock sekarang
    $hitung1 = mysqli_query($conn,"select * from produk where id_produk='$idproduk'");
    $hitung2 = mysqli_fetch_array($hitung1);
    $stocksekarang = $hitung2['stock']; //stock barang saat ini

    if($stocksekarang>=$qty){

        $selisih = $stocksekarang-$qty;

        //stock cukup
        $insert = mysqli_query($conn,"insert into detail_pesanan (id_pesanan,id_produk,qty) values ('$idp','$idproduk','$qty')");
        $update = mysqli_query($conn,"update produk set stock='$selisih' where id_produk='$idproduk'");

        if($insert&&$update){
            header('location:view.php?idp='.$idp);
        } else {
            echo '
            <script>alert("Gagal menambah pesanan baru");
            window.location.herf="view.php?idp='.$idp.'"
            </script>
            ';
        }
    } else {
        //stock tidak cukup
        echo '
            <script>alert("Stock barang tidak cukup");
            window.location.herf="view.php?idp='.$idp.'"
            </script>
            ';
    }
}

//menambah barang masuk
if(isset($_POST['barangmasuk'])){
    $idproduk = $_POST['id_produk'];
    $qty = $_POST['qty'];

    $insertb = mysqli_query($conn,"insert into masuk (id_produk,qty) values('$idproduk','$qty')");

    if($insertb){
        header('location:masuk.php');
    } else {
        echo '
            <script>alert("Gagal");
            window.location.herf="masuk.php"
            </script>
            ';
    }
}

//hapus produk pesanan
if(isset($_POST['hapusprodukpesanan'])){
    $idp = $_POST['idp']; //iddetailpesanan
    $idpr = $_POST['idpr'];
    $idorder = $_POST['id_order'];

    //Cek qty sekarang
    $cek1 = mysqli_query($conn,"select * from detail_pesanan where id_detail_pesanan='$idp'");
    $cek2 = mysqli_fetch_array($cek1);
    $qtysekarang = $cek2['qty'];

    //Cek stock sekarang
    $cek3 = mysqli_query($conn,"select * from produk where id_produk='$idpr'");
    $cek4 = mysqli_fetch_array($cek3);
    $stocksekarang = $cek4['stock'];

    $hitung = $stocksekarang+$qtysekarang;

    $update = mysqli_query($conn,"update produk set stock='$hitung' where id_produk='$idpr'"); //update stock
    $hapus = mysqli_query($conn,"delete from detail_pesanan where id_produk='$idpr' and id_detail_pesanan='$idp'");

    if($update&&$hapus){
        header('location:view.php?idp='.$idorder);
    } else {
        echo '
            <script>alert("Gagal menghapus barang");
            window.location.herf="view.php?idp='.$idorder.'"
            </script>
            ';
    }
}

//edit barang
if(isset($_POST['editbarang'])){
    $namaproduk = $_POST['nama_produk'];
    $desc = $_POST['deskripsi'];
    $harga = $_POST['harga'];
    $idp = $_POST['idp'];

    echo "Nama_Produk: $namaproduk, Deskripsi: $desc, Harga: $harga, Id_Produk: $idp";

    $query = mysqli_query($conn,"update produk set nama_produk='$namaproduk', deskripsi='$desc', harga='$harga' where id_produk='$idp'");

    if($query){
        header('location:stock.php');
    } else {
        echo '
            <script>alert("Gagal");
            window.location.herf="stock.php"
            </script>
            ';
    }
}

//Hapus barang
if(isset($_POST['hapusbarang'])){
    $idp =$_POST['idp'];

    $query = mysqli_query($conn,"delete from produk where id_produk='$idp'");

    if($query){
        header('location:stock.php');
    } else {
        echo '
            <script>alert("Gagal");
            window.location.herf="stock.php"
            </script>
            ';
    }
}

//edit pelanggan
if(isset($_POST['editpelanggan'])){
    $namapelanggan = $_POST['nama_pelanggan'];
    $notelp = $_POST['no_telp'];
    $alamat = $_POST['alamat'];
    $idpl = $_POST['idpl'];

    $query = mysqli_query($conn,"update pelanggan set nama_pelanggan='$namapelanggan', no_telp='$notelp', alamat='$alamat' where id_pelanggan='$idpl'");

    if($query){
        header('location:pelanggan.php');
    } else {
        echo '
            <script>alert("Gagal");
            window.location.herf="pelanggan.php"
            </script>
            ';
    }
}

//Hapus pelanggan
if(isset($_POST['hapuspelanggan'])){
    $idpl =$_POST['idpl'];

    $query = mysqli_query($conn,"delete from pelanggan where id_pelanggan='$idpl'");

    if($query){
        header('location:pelanggan.php');
    } else {
        echo '
            <script>alert("Gagal");
            window.location.herf="pelanggan.php"
            </script>
            ';
    }
}

//mengubah data barang masuk
if (isset($_POST['editdatabarangmasuk'])) {
    // Get the values from the form
    $idmasuk = $_POST['idm'];
    $qty = $_POST['qty'];

    // Make sure to validate and sanitize the inputs
    $qty = mysqli_real_escape_string($conn, $qty);

    // Update query
    $query = "UPDATE masuk SET qty = '$qty' WHERE id_masuk = '$idmasuk'";

    // Execute the query
    $update = mysqli_query($conn, $query);

    if ($update) {
        // Instead of using JavaScript alert and redirect, use PHP header for redirect
        header("Location: masuk.php"); // Redirect to the same page
        exit(); // Always call exit() after header redirect to prevent further execution
    } else {
        echo "<script>alert('Data gagal diperbarui');</script>";
    }
}



?>  