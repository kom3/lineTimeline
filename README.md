# lineTimeline
Line Timeline API

Ini merupakan proyek sampingan atau proyek waktu luang, dengan class ini, kamu bisa mengakses berbagai api yang tersedia di Line Timeline.

<h1>Setup</h1>
<p>Lakukan yang kamu ketahui dan percayai!</p>
<h1>How to Use</h1>
<h3>Class</h3>
<p>Terlebih dahulu require/import/include file class (jika berbeda file), lalu panggil class menggunakan syntax biasa</p>
<pre>$app = new LineTimeline();</pre>
<h3>Function</h3>
<h4>Set Session</h4>
<p>Anda harus mengambil cookies/session yang diperlukan, anda bisa mengikuti video https://youtu.be/beqZiKzC8HQ (Thanks Fadhiil). Pemanggilan fungsi yang lainnya tidak akan berjalan jika anda belum memanggil fungsi ini.</p>
<pre>$app->setSession('COOKIESHERE');</pre>
<h4>Post to Timeline</h4>
<pre>$app->postTimeline('Hehehehe, welcome AskaEks', 1, false);</pre>
<p>Informasi untuk parameters bisa dilihat didalam script, anda bisa mendapatkannya juga dengan membaca script</p>
<h1>Penggunaan Lanjutan</h1>
<p><i style="color: red;">Perhatian!</i> Tutorial setelah ini membutuhkan pengetahuan dasar PHP, jika anda belum mengenal syntax, atau apa itu PHP, anda bisa mengunjungi laman <i>php.net</i></p>
<h4>Penggunaan Callback</h4>
<p>Apa sih callback itu ? kepo.... Berikut contoh penggunaan <b>like a post</b> biasa</p>
<pre>$return = $app->likePost('2331888482828325', 0, false);
if($return){
  // If Success
} else {
  // If Error
}</pre>
<p>Berikut contoh jika menggunakan callback untuk fungsi diatas</p>
<pre>$app->likePost('2331888482828325', 0, false, function($error){
  if(!$error){
    // If Success
  } else {
    // If Error
  }
});</pre>
<p>Berikut contoh penggunaan like timeline dengan callback</p>
<pre>$app->likeTimeline(2, 0, 1, 1, function($error){
  if($error){
    echo "Gagal Like!";
  } else {
    echo "Sukses Like!";
  }
});</pre>
<h1>License & Notice</h1>
<p>Code ini berlisensi MIT, dimohon untuk tidak menghapus credit yang tersedia dan selalu sertaka file LICENSE, dipublish dengan tujuan pembelajaran semata, bukan untuk merugikan orang lain, atau untuk melakukan spam. Perbuatan tersebut terlarang dan kami/saya tidak bertanggung jawab atas segala kerugian yang ditimbulkan. Berani berbuat berani bertanggung jawab!</p>
<h1>Legal</h1>
<p>This code is in no way affiliated with, authorized, maintained, sponsored or endorsed by Line or/and Naver Japan or any of its affiliates or subsidiaries. Use at your own risk.</p>
