<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Challenge;
use App\Models\ChallengeLevel;
use App\Models\Course;
use App\Models\User;

class ChallengeSeeder extends Seeder
{
    public function run(): void
    {
        $course1 = Course::where('slug', 'belajar-laravel-10-nol-hingga-expert')->first();
        $course2 = Course::where('slug', 'python-data-science-machine-learning')->first();
        $course3 = Course::where('slug', 'html-css-untuk-pemula')->first();

        // ──────────────────────────────────────────────────────────────
        // COURSE 1 – Laravel: Problem Solving Challenge
        // ──────────────────────────────────────────────────────────────
        if ($course1) {
            $c1ps = Challenge::create([
                'course_id'        => $course1->id,
                'tutor_id'         => $course1->tutor_id,
                'title'            => 'Laravel Problem Solving',
                'description'      => 'Uji pemahaman kamu tentang konsep Laravel: routing, Eloquent, middleware, dan lainnya!',
                'type'             => 'problem_solving',
                'difficulty'       => 'medium',
                'time_limit'       => 45,
                'points_per_level' => 15,
                'icon'             => '🚀',
                'is_active'        => true,
            ]);

            $levels = [
                [1, 'Routing Dasar', 'Kode route Laravel mana yang benar untuk menangani method POST ke /login?',
                 ['type'=>'multiple_choice','question'=>'Pilih sintaks route yang benar untuk POST /login','options'=>['Route::post(\'/login\', LoginController::class);','Route::get(\'/login\', LoginController::class);','Route::fetch(\'/login\', LoginController::class);','Route::submit(\'/login\', LoginController::class);'],'answer_index'=>0,'explanation'=>'Route::post() digunakan untuk metode HTTP POST. Route::get() untuk GET, Route::put() untuk PUT, dst.'],
                 'Route::post(\'/login\', LoginController::class);', ['HTTP method untuk form login biasanya?', 'Route::___(path, handler)']],

                [2, 'Eloquent Query', 'Apa output dari query Eloquent berikut?',
                 ['type'=>'code_output','code'=>"// Tabel users memiliki 5 baris\n\$count = User::where('role', 'admin')->count();\n// Ada 2 admin di database\necho \$count;",'language'=>'php','options'=>['5','2','null','Error'],'answer_index'=>1,'explanation'=>'where()->count() menghitung baris yang memenuhi kondisi. Ada 2 admin, maka hasilnya 2.'],
                 '2', ['where() memfilter data', 'count() menghitung hasil filter']],

                [3, 'Middleware', 'Apa kegunaan utama middleware di Laravel?',
                 ['type'=>'multiple_choice','question'=>'Apa fungsi utama middleware dalam aplikasi Laravel?','options'=>['Mengatur tampilan view','Memfilter HTTP request sebelum mencapai controller','Menyimpan data ke database','Mengompres file CSS dan JS'],'answer_index'=>1,'explanation'=>'Middleware berjalan di antara request dan controller. Contoh: auth middleware mengecek apakah user sudah login sebelum mengakses halaman.'],
                 'Memfilter HTTP request sebelum mencapai controller', ['Middleware berjalan "di tengah" alur request', 'Contoh: cek autentikasi user']],

                [4, 'Eloquent Relationship', 'Relasi apa yang digunakan jika satu User bisa memiliki banyak Post?',
                 ['type'=>'multiple_choice','question'=>'Di model User, relasi apa yang dipakai untuk mengambil semua Post milik user tersebut?','options'=>['belongsTo','belongsToMany','hasMany','hasOne'],'answer_index'=>2,'explanation'=>'hasMany() digunakan di sisi "satu" (User) untuk mengambil banyak data yang terkait (Post). Post menggunakan belongsTo(User::class).'],
                 'hasMany', ['Satu user → banyak post', 'Model mana yang "memiliki" data?']],

                [5, 'Migration', 'Perintah artisan apa yang digunakan untuk membuat file migration baru?',
                 ['type'=>'multiple_choice','question'=>'Perintah artisan yang benar untuk membuat migration baru adalah?','options'=>['php artisan make:migration','php artisan create:migration','php artisan db:migration','php artisan generate:migration'],'answer_index'=>0,'explanation'=>'"make:migration" adalah perintah artisan standar untuk membuat file migration baru di folder database/migrations.'],
                 'php artisan make:migration', ['Semua perintah "buat" di artisan menggunakan make:', 'Diikuti nama file migration']],

                [6, 'Blade Template', 'Kode Blade mana yang menampilkan variabel dengan aman (XSS-safe)?',
                 ['type'=>'multiple_choice','question'=>'Cara aman menampilkan variabel $nama di Blade (mencegah XSS)?','options'=>['{!! $nama !!}','{{ $nama }}','<?= $nama ?>','<% $nama %>'],'answer_index'=>1,'explanation'=>'{{ }} secara otomatis meng-escape HTML untuk mencegah XSS. {!! !!} menampilkan HTML mentah (berbahaya jika dari input user).'],
                 '{{ $nama }}', ['Blade punya dua cara menampilkan variabel', 'Mana yang auto-escape HTML?']],

                [7, 'Request Validation', 'Temukan bug di kode validasi ini!',
                 ['type'=>'code_debug','code'=>"\$request->validates([\n    'email' => 'required|email',\n    'password' => 'required|min:8',\n]);",'language'=>'php','bug'=>'Method validates() tidak ada, seharusnya validate()','options'=>['Tidak ada bug','Method validates() salah, seharusnya validate()','Aturan email salah','min:8 seharusnya min:6'],'answer_index'=>1,'explanation'=>'Method yang benar adalah $request->validate() (tanpa "s"). validates() tidak ada dan akan melempar error BadMethodCallException.'],
                 'Method validates() salah, seharusnya validate()', ['Perhatikan nama method dengan cermat', 'Ada typo di nama method?']],

                [8, 'Eloquent Mass Assignment', 'Apa kegunaan $fillable di model Eloquent?',
                 ['type'=>'multiple_choice','question'=>'Properti $fillable di model Eloquent berguna untuk?','options'=>['Menentukan kolom yang bisa diisi via mass assignment','Menentukan kolom yang ditampilkan di JSON','Membuat kolom otomatis terisi','Mengenkripsi data sensitif'],'answer_index'=>0,'explanation'=>'$fillable melindungi dari mass assignment vulnerability. Hanya kolom yang ada di $fillable yang bisa diisi dengan Model::create() atau $model->fill().'],
                 'Menentukan kolom yang bisa diisi via mass assignment', ['Berhubungan dengan keamanan', 'Model::create() bergantung pada properti ini']],

                [9, 'Artisan Tinker', 'Apa yang dilakukan perintah php artisan tinker?',
                 ['type'=>'multiple_choice','question'=>'Perintah "php artisan tinker" digunakan untuk?','options'=>['Menjalankan server development','Membuka REPL interaktif untuk bereksperimen dengan kode Laravel','Mengoptimasi performa aplikasi','Mengimpor data ke database'],'answer_index'=>1,'explanation'=>'Tinker adalah REPL (Read-Eval-Print Loop) yang memungkinkan kamu menjalankan kode PHP/Eloquent langsung dari terminal untuk testing atau eksplorasi data.'],
                 'Membuka REPL interaktif untuk bereksperimen dengan kode Laravel', ['REPL = Read Eval Print Loop', 'Berguna untuk test query Eloquent']],

                [10, 'N+1 Problem', 'Kode mana yang menghindari N+1 query problem?',
                 ['type'=>'multiple_choice','question'=>'Dari dua opsi ini, mana yang menghindari N+1 query?\nA: $posts = Post::all(); foreach($posts as $p) { $p->user->name; }\nB: $posts = Post::with(\'user\')->get(); foreach($posts as $p) { $p->user->name; }','options'=>['Opsi A','Opsi B','Keduanya sama','Keduanya buruk'],'answer_index'=>1,'explanation'=>'Opsi B menggunakan eager loading with(\'user\') sehingga hanya 2 query. Opsi A menghasilkan 1+N query (1 untuk semua post, N untuk tiap user), yang sangat lambat dengan data besar.'],
                 'Opsi B', ['N+1 = 1 query utama + N query tambahan', 'Eager loading dengan with() memuat semua relasi sekaligus']],
            ];

            foreach ($levels as [$num, $title, $instructions, $data, $answer, $hints]) {
                ChallengeLevel::create([
                    'challenge_id'  => $c1ps->id,
                    'level_number'  => $num,
                    'title'         => $title,
                    'instructions'  => $instructions,
                    'puzzle_data'   => $data,
                    'answer'        => $answer,
                    'hints'         => $hints,
                ]);
            }

            // Laravel: Puzzle & Logic
            $c1pl = Challenge::create([
                'course_id'        => $course1->id,
                'tutor_id'         => $course1->tutor_id,
                'title'            => 'Laravel Logic Puzzle',
                'description'      => 'Asah logika pemrograman PHP dan Laravel dengan teka-teki seru!',
                'type'             => 'puzzle_logic',
                'difficulty'       => 'easy',
                'time_limit'       => 30,
                'points_per_level' => 10,
                'icon'             => '🧩',
                'is_active'        => true,
            ]);

            $puzzleLevels = [
                [1, 'PHP String', 'Apa output kode PHP ini?',
                 ['type'=>'code_output','code'=>'<?php\n$s = "Laravel";\necho strlen($s);','language'=>'php','options'=>['6','7','8','5'],'answer_index'=>1,'explanation'=>'strlen("Laravel") = 7 karena kata "Laravel" terdiri dari 7 huruf: L-a-r-a-v-e-l'],
                 '7', ['Hitung huruf dalam "Laravel"', 'strlen() menghitung panjang string']],

                [2, 'Deret Versi', 'Laravel 6, 7, 8, 9, 10, ?',
                 ['type'=>'number_sequence','sequence'=>[6,7,8,9,10,null],'blank_index'=>5,'pattern'=>'+1','options'=>['10','11','12','13'],'answer_index'=>1,'explanation'=>'Versi Laravel bertambah 1 setiap rilis utama: 6→7→8→9→10→11'],
                 '11', ['Pola sederhana +1', 'Versi Laravel terbaru adalah?']],

                [3, 'Anagram PHP', 'Susun huruf ini menjadi kata yang berhubungan dengan web!',
                 ['type'=>'anagram','scrambled'=>'TUROE','answer_word'=>'ROUTE','options'=>['TROUT','ROUTE','OUTER','TOWER'],'answer_index'=>1,'explanation'=>'R-O-U-T-E = ROUTE, konsep penting di Laravel untuk mendefinisikan URL aplikasi'],
                 'ROUTE', ['Konsep penting di Laravel', 'Menghubungkan URL ke Controller']],

                [4, 'MVC Teka-teki', 'Saya mengatur tampilan yang dilihat user. Apa saya?',
                 ['type'=>'riddle','riddle'=>'Dalam arsitektur MVC Laravel, saya adalah bagian yang mengatur apa yang DILIHAT oleh pengguna. Saya berisi kode HTML dan Blade. Saya adalah?','options'=>['Model','Controller','View','Middleware'],'answer_index'=>2,'explanation'=>'View adalah komponen MVC yang mengatur tampilan. Di Laravel, view disimpan di resources/views/ dan menggunakan Blade templating engine.'],
                 'View', ['Huruf V dalam MVC', 'File .blade.php ada di folder ini']],

                [5, 'Status Code', 'HTTP status code untuk "Not Found" adalah?',
                 ['type'=>'math','expression'=>'HTTP status code untuk halaman yang TIDAK DITEMUKAN (Not Found) adalah: 4??','options'=>['400','401','403','404'],'answer_index'=>3,'explanation'=>'404 adalah status code HTTP untuk "Not Found". 400=Bad Request, 401=Unauthorized, 403=Forbidden, 500=Server Error.'],
                 '404', ['4xx berarti error dari sisi client', 'Kamu pasti pernah lihat halaman ini']],
            ];

            foreach ($puzzleLevels as [$num, $title, $instructions, $data, $answer, $hints]) {
                ChallengeLevel::create([
                    'challenge_id'  => $c1pl->id,
                    'level_number'  => $num,
                    'title'         => $title,
                    'instructions'  => $instructions,
                    'puzzle_data'   => $data,
                    'answer'        => $answer,
                    'hints'         => $hints,
                ]);
            }
        }

        // ──────────────────────────────────────────────────────────────
        // COURSE 2 – Python & Data Science: Problem Solving
        // ──────────────────────────────────────────────────────────────
        if ($course2) {
            $c2ps = Challenge::create([
                'course_id'        => $course2->id,
                'tutor_id'         => $course2->tutor_id,
                'title'            => 'Python Code Challenge',
                'description'      => 'Uji kemampuan Python kamu: sintaks, struktur data, NumPy, Pandas, hingga Machine Learning!',
                'type'             => 'problem_solving',
                'difficulty'       => 'medium',
                'time_limit'       => 60,
                'points_per_level' => 15,
                'icon'             => '🐍',
                'is_active'        => true,
            ]);

            $levels = [
                [1, 'List Comprehension', 'Apa output kode Python ini?',
                 ['type'=>'code_output','code'=>"nums = [1, 2, 3, 4, 5]\nresult = [x**2 for x in nums if x % 2 == 0]\nprint(result)",'language'=>'python','options'=>['[1, 4, 9, 16, 25]','[4, 16]','[2, 4]','[1, 9, 25]'],'answer_index'=>1,'explanation'=>'List comprehension: ambil x dari nums, filter x genap (2,4), lalu kuadratkan: [4, 16]'],
                 '[4, 16]', ['Filter dulu: angka genap saja', 'Lalu operasi x**2 = x pangkat 2']],

                [2, 'NumPy Array', 'Apa hasil operasi NumPy ini?',
                 ['type'=>'code_output','code'=>"import numpy as np\na = np.array([1, 2, 3])\nb = np.array([4, 5, 6])\nprint(a + b)",'language'=>'python','options'=>['[1,2,3,4,5,6]','[5,7,9]','[4,10,18]','Error'],'answer_index'=>1,'explanation'=>'NumPy melakukan operasi element-wise: [1+4, 2+5, 3+6] = [5, 7, 9]. Berbeda dengan list Python biasa yang akan digabung.'],
                 '[5, 7, 9]', ['NumPy melakukan operasi per elemen', 'Berbeda dari penjumlahan list biasa']],

                [3, 'Pandas DataFrame', 'Perintah apa untuk melihat 5 baris pertama DataFrame?',
                 ['type'=>'multiple_choice','question'=>'Cara melihat 5 baris pertama DataFrame df di Pandas adalah?','options'=>['df.top(5)','df.first(5)','df.head(5)','df.peek(5)'],'answer_index'=>2,'explanation'=>'df.head(n) menampilkan n baris pertama. Default n=5. Pasangannya adalah df.tail(n) untuk baris terakhir.'],
                 'df.head(5)', ['Method ini "mengintip" awal data', 'Pasangannya adalah .tail()']],

                [4, 'Missing Value', 'Perintah mana yang menghapus baris dengan nilai NaN?',
                 ['type'=>'multiple_choice','question'=>'Untuk menghapus semua baris yang mengandung nilai NaN di DataFrame df?','options'=>['df.remove_nan()','df.dropna()','df.fillna(0)','df.clean()'],'answer_index'=>1,'explanation'=>'dropna() menghapus baris (atau kolom) yang mengandung NaN. fillna() untuk mengisi NaN dengan nilai tertentu.'],
                 'df.dropna()', ['NaN = Not a Number, nilai kosong', 'drop = hapus, na = nilai kosong']],

                [5, 'Lambda Function', 'Apa output fungsi lambda ini?',
                 ['type'=>'code_output','code'=>"double = lambda x: x * 2\nresult = list(map(double, [1, 2, 3, 4]))\nprint(result)",'language'=>'python','options'=>['[1,2,3,4]','[2,4,6,8]','[1,4,9,16]','[2,3,4,5]'],'answer_index'=>1,'explanation'=>'lambda x: x*2 adalah fungsi yang mengalikan input dengan 2. map() menerapkan fungsi ke setiap elemen: [1*2, 2*2, 3*2, 4*2] = [2,4,6,8]'],
                 '[2, 4, 6, 8]', ['lambda adalah fungsi anonim singkat', 'map() menerapkan fungsi ke setiap elemen list']],

                [6, 'Train-Test Split', 'Berapa ukuran test set jika test_size=0.2 dari 100 data?',
                 ['type'=>'math','expression'=>'100 data × test_size=0.2 → ukuran test set = ?','options'=>['10','20','80','25'],'answer_index'=>1,'explanation'=>'test_size=0.2 berarti 20% untuk test. 100 × 0.2 = 20 data untuk test, 80 data untuk training.'],
                 '20', ['0.2 berarti berapa persen?', '100 × 0.2 = ?']],

                [7, 'Confusion Matrix', 'Apa yang dimaksud Precision?',
                 ['type'=>'multiple_choice','question'=>'Dalam evaluasi model ML, Precision mengukur?','options'=>['Dari semua aktual positif, berapa yang diprediksi benar','Dari semua prediksi positif, berapa yang benar-benar positif','Akurasi keseluruhan model','Kecepatan model memprediksi'],'answer_index'=>1,'explanation'=>'Precision = TP / (TP + FP). Dari semua yang diprediksi positif, berapa yang benar-benar positif. Recall = TP / (TP + FN) mengukur dari semua aktual positif.'],
                 'Dari semua prediksi positif, berapa yang benar-benar positif', ['Precision = ketepatan prediksi positif', 'TP / (TP + FP)']],

                [8, 'Python Bug', 'Temukan bug di kode ini!',
                 ['type'=>'code_debug','code'=>"data = [3, 1, 4, 1, 5, 9, 2, 6]\ndata.sort(reversed=True)\nprint(data)",'language'=>'python','bug'=>'Parameter sort() yang benar adalah reverse=True, bukan reversed=True','options'=>['Tidak ada bug','Parameter reversed=True salah, seharusnya reverse=True','data tidak bisa di-sort','print() salah'],'answer_index'=>1,'explanation'=>'sort() menerima parameter reverse=True (bukan reversed). reversed() adalah fungsi built-in terpisah yang mengembalikan iterator.'],
                 'Parameter reversed=True salah, seharusnya reverse=True', ['Perhatikan nama parameter dengan cermat', 'reverse (kata sifat) vs reversed (kata kerja)']],

                [9, 'Overfitting', 'Model dengan akurasi training 99% tapi test 60% mengalami?',
                 ['type'=>'multiple_choice','question'=>'Model ML dengan training accuracy 99% tapi test accuracy 60% mengalami masalah apa?','options'=>['Underfitting','Overfitting','Bias tinggi','Model sudah sempurna'],'answer_index'=>1,'explanation'=>'Overfitting = model terlalu "hafal" data training sehingga buruk pada data baru. Solusi: lebih banyak data, regularisasi, atau cross-validation.'],
                 'Overfitting', ['Training bagus, test buruk = ?', 'Model terlalu "hafal" data training']],

                [10, 'Slicing DataFrame', 'Apa output kode Pandas ini?',
                 ['type'=>'code_output','code'=>"import pandas as pd\ndf = pd.DataFrame({'A': [10,20,30,40,50]})\nprint(df['A'].mean())",'language'=>'python','options'=>['10','30','50','150'],'answer_index'=>1,'explanation'=>'mean() menghitung rata-rata. (10+20+30+40+50)/5 = 150/5 = 30'],
                 '30', ['mean() = rata-rata', '(10+20+30+40+50) ÷ 5 = ?']],
            ];

            foreach ($levels as [$num, $title, $instructions, $data, $answer, $hints]) {
                ChallengeLevel::create([
                    'challenge_id'  => $c2ps->id,
                    'level_number'  => $num,
                    'title'         => $title,
                    'instructions'  => $instructions,
                    'puzzle_data'   => $data,
                    'answer'        => $answer,
                    'hints'         => $hints,
                ]);
            }

            // Python: Puzzle & Logic
            $c2pl = Challenge::create([
                'course_id'        => $course2->id,
                'tutor_id'         => $course2->tutor_id,
                'title'            => 'Data Science Logic Puzzle',
                'description'      => 'Asah logika berpikir data dengan pola angka, teka-teki statistik, dan puzzle AI!',
                'type'             => 'puzzle_logic',
                'difficulty'       => 'easy',
                'time_limit'       => 30,
                'points_per_level' => 10,
                'icon'             => '📊',
                'is_active'        => true,
            ]);

            $puzzleLevels = [
                [1, 'Deret Data', 'Temukan angka berikutnya!',
                 ['type'=>'number_sequence','sequence'=>[2,4,8,16,32,null],'blank_index'=>5,'pattern'=>'×2','options'=>['48','56','64','72'],'answer_index'=>2,'explanation'=>'Deret geometri dengan rasio 2: setiap angka dikali 2. 32 × 2 = 64'],
                 '64', ['Setiap angka dikali berapa?', '32 × 2 = ?']],

                [2, 'Anagram AI', 'Susun huruf ini menjadi istilah Machine Learning!',
                 ['type'=>'anagram','scrambled'=>'LEMOD','answer_word'=>'MODEL','options'=>['MODES','MODEL','MOLED','DEMON'],'answer_index'=>1,'explanation'=>'M-O-D-E-L = MODEL, komponen utama dalam Machine Learning yang dilatih untuk membuat prediksi'],
                 'MODEL', ['Dilatih dengan data training', 'Huruf: M-O-D-E-L']],

                [3, 'Teka-teki Statistik', 'Nilai tengah dari data yang sudah diurutkan adalah?',
                 ['type'=>'riddle','riddle'=>'Saya adalah nilai tengah dari sekumpulan data yang sudah diurutkan. Saya tidak terpengaruh oleh outlier ekstrem. Saya bukan Mean (rata-rata). Saya adalah?','options'=>['Modus','Median','Varians','Standar Deviasi'],'answer_index'=>1,'explanation'=>'Median adalah nilai tengah data terurut. Keunggulan median: tidak terpengaruh outlier, berbeda dengan mean yang bisa bergeser drastis karena data ekstrem.'],
                 'Median', ['Nilai tengah, bukan rata-rata', 'Tidak terpengaruh oleh data ekstrem']],

                [4, 'Deret Fibonacci Data', 'Angka berapa yang hilang?',
                 ['type'=>'number_sequence','sequence'=>[1,1,2,3,5,8,null,21],'blank_index'=>6,'pattern'=>'Fibonacci','options'=>['11','12','13','15'],'answer_index'=>2,'explanation'=>'Deret Fibonacci: setiap angka = jumlah dua sebelumnya. 5+8=13'],
                 '13', ['Jumlahkan dua angka sebelumnya', '5 + 8 = ?']],

                [5, 'Akurasi Model', 'Model prediksi benar 80 dari 100 data. Akurasinya?',
                 ['type'=>'math','expression'=>'Akurasi = prediksi_benar ÷ total_data × 100%\n= 80 ÷ 100 × 100% = ?%','options'=>['0.8%','8%','80%','800%'],'answer_index'=>2,'explanation'=>'Akurasi = 80/100 × 100% = 80%. Ini adalah metrik evaluasi paling dasar: persentase prediksi yang benar dari total data.'],
                 '80%', ['Bagi jumlah benar dengan total data', 'Kalikan dengan 100 untuk persen']],
            ];

            foreach ($puzzleLevels as [$num, $title, $instructions, $data, $answer, $hints]) {
                ChallengeLevel::create([
                    'challenge_id'  => $c2pl->id,
                    'level_number'  => $num,
                    'title'         => $title,
                    'instructions'  => $instructions,
                    'puzzle_data'   => $data,
                    'answer'        => $answer,
                    'hints'         => $hints,
                ]);
            }
        }

        // ──────────────────────────────────────────────────────────────
        // COURSE 3 – HTML & CSS: Problem Solving + Puzzle
        // ──────────────────────────────────────────────────────────────
        if ($course3) {
            $c3ps = Challenge::create([
                'course_id'        => $course3->id,
                'tutor_id'         => $course3->tutor_id,
                'title'            => 'HTML & CSS Code Challenge',
                'description'      => 'Uji pengetahuan kamu tentang struktur HTML, CSS styling, dan web development dasar!',
                'type'             => 'problem_solving',
                'difficulty'       => 'easy',
                'time_limit'       => 30,
                'points_per_level' => 10,
                'icon'             => '🌐',
                'is_active'        => true,
            ]);

            $levels = [
                [1, 'Tag HTML', 'Tag HTML mana yang digunakan untuk membuat heading terbesar?',
                 ['type'=>'multiple_choice','question'=>'Tag HTML untuk membuat judul/heading terbesar (paling penting) adalah?','options'=>['<h6>','<heading>','<h1>','<title>'],'answer_index'=>2,'explanation'=>'<h1> adalah heading paling besar dan paling penting (1-6, dari besar ke kecil). <title> untuk judul tab browser. <heading> bukan tag HTML valid.'],
                 '<h1>', ['Heading punya level 1-6', 'Level 1 = paling besar']],

                [2, 'Struktur HTML', 'Tag HTML mana yang WAJIB ada di setiap halaman web?',
                 ['type'=>'multiple_choice','question'=>'Dari pilihan berikut, tag mana yang merupakan struktur WAJIB halaman HTML5?','options'=>['<html>, <head>, <body>','<html>, <header>, <footer>','<html>, <nav>, <main>','<html>, <section>, <article>'],'answer_index'=>0,'explanation'=>'Struktur dasar HTML5: <!DOCTYPE html>, <html>, <head> (metadata), dan <body> (konten). Header/footer/nav adalah elemen semantik opsional.'],
                 '<html>, <head>, <body>', ['Tiga elemen induk utama HTML', 'head = metadata, body = konten']],

                [3, 'CSS Selector', 'CSS selector mana yang menarget elemen dengan class "btn"?',
                 ['type'=>'multiple_choice','question'=>'Cara menulis CSS selector untuk menarget semua elemen dengan class="btn"?','options'=>['#btn { }','btn { }','.btn { }','*btn { }'],'answer_index'=>2,'explanation'=>'.btn (titik + nama class) menarget semua elemen dengan class="btn". # untuk id, tanpa prefix untuk tag HTML, * untuk semua elemen.'],
                 '.btn { }', ['Class selector menggunakan simbol titik (.)', 'ID selector menggunakan #']],

                [4, 'CSS Box Model', 'Properti CSS mana yang mengatur jarak di DALAM border elemen?',
                 ['type'=>'multiple_choice','question'=>'Untuk mengatur jarak antara konten dan border (di dalam elemen), gunakan?','options'=>['margin','padding','border-spacing','spacing'],'answer_index'=>1,'explanation'=>'padding = jarak dalam border (antara konten dan border). margin = jarak luar border (antara elemen dan elemen lain). Ini bagian dari CSS Box Model.'],
                 'padding', ['Box Model: content → padding → border → margin', 'Dari dalam ke luar']],

                [5, 'HTML Link', 'Tag apa untuk membuat hyperlink?',
                 ['type'=>'multiple_choice','question'=>'Untuk membuat teks yang bisa diklik sebagai link ke halaman lain, gunakan tag?','options'=>['<link>','<a>','<href>','<url>'],'answer_index'=>1,'explanation'=>'<a href="url">teks</a> adalah tag anchor untuk hyperlink. <link> digunakan di <head> untuk menghubungkan stylesheet eksternal, bukan untuk hyperlink yang terlihat.'],
                 '<a>', ['Anchor tag', '<a href="...">teks link</a>']],

                [6, 'Flexbox', 'Property CSS apa yang mengaktifkan Flexbox?',
                 ['type'=>'multiple_choice','question'=>'Untuk menggunakan Flexbox pada sebuah container, property CSS yang harus ditambahkan adalah?','options'=>['display: block','display: flex','flex: active','layout: flexbox'],'answer_index'=>1,'explanation'=>'display: flex pada elemen parent mengaktifkan Flexbox. Setelah itu bisa gunakan justify-content, align-items, dll. untuk mengatur layout anak-anaknya.'],
                 'display: flex', ['Property "display" mengatur jenis layout', 'flex = Flexible Box Layout']],

                [7, 'CSS Color', 'Nilai warna CSS mana yang menghasilkan warna MERAH murni?',
                 ['type'=>'multiple_choice','question'=>'Warna merah murni dalam format HEX CSS adalah?','options'=>['#000000','#ffffff','#ff0000','#0000ff'],'answer_index'=>2,'explanation'=>'#ff0000 = RGB(255,0,0) = merah murni. #000000 = hitam, #ffffff = putih, #0000ff = biru. Format HEX: #RRGGBB (merah-hijau-biru, 00-ff)'],
                 '#ff0000', ['Format HEX: #RRGGBB', 'ff=255 (maksimal), 00=0 (kosong)']],

                [8, 'HTML Semantik', 'Tag semantik mana yang tepat untuk navigasi utama?',
                 ['type'=>'multiple_choice','question'=>'Untuk membuat bagian navigasi utama website, tag semantik HTML5 yang tepat adalah?','options'=>['<div id="nav">','<navigation>','<nav>','<menu>'],'answer_index'=>2,'explanation'=>'<nav> adalah elemen semantik HTML5 untuk navigasi. Lebih baik dari <div id="nav"> karena bermakna bagi mesin pencari dan screen reader.'],
                 '<nav>', ['HTML5 punya banyak tag semantik', 'nav = navigation']],

                [9, 'CSS Responsive', 'Media query CSS untuk layar dengan lebar maksimal 768px?',
                 ['type'=>'multiple_choice','question'=>'Sintaks media query yang benar untuk menarget layar dengan lebar ≤ 768px (tablet/mobile)?','options'=>['@media screen > 768px','@media (max-width: 768px)','@media width: 768px','@screen max(768px)'],'answer_index'=>1,'explanation'=>'@media (max-width: 768px) { } menerapkan style jika lebar layar ≤ 768px. Ini adalah cara standar membuat desain responsif untuk tablet dan mobile.'],
                 '@media (max-width: 768px)', ['@media adalah aturan CSS untuk kondisi layar', 'max-width = maksimum lebar']],

                [10, 'CSS Bug', 'Temukan kesalahan dalam kode CSS ini!',
                 ['type'=>'code_debug','code'=>".container {\n    width: 100%\n    max-width: 1200px;\n    margin: 0 auto;\n}",'language'=>'css','bug'=>'Titik koma (;) hilang setelah width: 100%','options'=>['Tidak ada bug','Titik koma hilang setelah width: 100%','margin: 0 auto salah','max-width tidak valid'],'answer_index'=>1,'explanation'=>'width: 100% tidak diakhiri titik koma (;). Di CSS, setiap deklarasi harus diakhiri ; atau browser mengabaikan property tersebut dan mungkin property berikutnya juga.'],
                 'Titik koma hilang setelah width: 100%', ['Setiap baris CSS diakhiri dengan?', 'Perhatikan baris width: 100%']],
            ];

            foreach ($levels as [$num, $title, $instructions, $data, $answer, $hints]) {
                ChallengeLevel::create([
                    'challenge_id'  => $c3ps->id,
                    'level_number'  => $num,
                    'title'         => $title,
                    'instructions'  => $instructions,
                    'puzzle_data'   => $data,
                    'answer'        => $answer,
                    'hints'         => $hints,
                ]);
            }

            // HTML/CSS: Puzzle
            $c3pl = Challenge::create([
                'course_id'        => $course3->id,
                'tutor_id'         => $course3->tutor_id,
                'title'            => 'Web Dev Logic Puzzle',
                'description'      => 'Teka-teki seru seputar dunia web development: HTML, CSS, dan konsep dasar internet!',
                'type'             => 'puzzle_logic',
                'difficulty'       => 'easy',
                'time_limit'       => 25,
                'points_per_level' => 10,
                'icon'             => '🎨',
                'is_active'        => true,
            ]);

            $puzzleLevels = [
                [1, 'Anagram Web', 'Susun huruf ini menjadi konsep web!',
                 ['type'=>'anagram','scrambled'=>'YLETS','answer_word'=>'STYLE','options'=>['SLYTE','STYLE','STLEY','LYSТЕ'],'answer_index'=>1,'explanation'=>'S-T-Y-L-E = STYLE, digunakan dalam HTML sebagai atribut inline CSS atau tag <style>'],
                 'STYLE', ['Berhubungan dengan tampilan/CSS', 'Bisa berupa atribut HTML atau tag']],

                [2, 'Deret Heading', 'Urutan heading HTML dari terbesar ke terkecil?',
                 ['type'=>'number_sequence','sequence'=>[1,2,3,4,5,null],'blank_index'=>5,'pattern'=>'+1','options'=>['5','6','7','8'],'answer_index'=>1,'explanation'=>'Heading HTML: h1 sampai h6. Paling besar h1, paling kecil h6. Tidak ada h7.'],
                 '6', ['Heading HTML ada berapa level?', 'h1 sampai h?']],

                [3, 'Teka-teki Web', 'Saya adalah "kerangka" halaman web. Apa saya?',
                 ['type'=>'riddle','riddle'=>'Saya mendefinisikan STRUKTUR dan KONTEN halaman web. Saya menggunakan tag dan elemen. Tanpa saya, browser tidak bisa menampilkan halaman. CSS mempercantik saya. Apa saya?','options'=>['CSS','JavaScript','HTML','PHP'],'answer_index'=>2,'explanation'=>'HTML (HyperText Markup Language) adalah bahasa markup yang mendefinisikan struktur dan konten halaman web. CSS mengatur tampilan, JavaScript menambahkan interaktivitas.'],
                 'HTML', ['Singkatan: HyperText Markup Language', 'Bahasa struktur halaman web']],

                [4, 'Prioritas CSS', 'Dari pilihan selector berikut, mana yang prioritasnya TERTINGGI?',
                 ['type'=>'multiple_choice','question'=>'Dalam CSS specificity (prioritas), urutan dari TERTINGGI ke terendah adalah?','options'=>['tag < class < id < inline','inline < id < class < tag','tag < id < class < inline','class < tag < inline < id'],'answer_index'=>0,'explanation'=>'Specificity CSS: inline style (1000) > id (100) > class (10) > tag (1). Makin spesifik selector, makin tinggi prioritasnya. !important mengalahkan semua.'],
                 'tag < class < id < inline', ['Inline style ada di atribut HTML langsung', 'id lebih spesifik dari class']],

                [5, 'HTTP Status', 'Website berhasil dimuat. Status code-nya?',
                 ['type'=>'math','expression'=>'HTTP status code untuk halaman BERHASIL dimuat (OK) = ?','options'=>['200','301','404','500'],'answer_index'=>0,'explanation'=>'200 OK = request berhasil. 301 = Redirect permanen. 404 = Not Found. 500 = Server Error. 2xx selalu berarti sukses.'],
                 '200', ['2xx = sukses', 'OK = berhasil']],
            ];

            foreach ($puzzleLevels as [$num, $title, $instructions, $data, $answer, $hints]) {
                ChallengeLevel::create([
                    'challenge_id'  => $c3pl->id,
                    'level_number'  => $num,
                    'title'         => $title,
                    'instructions'  => $instructions,
                    'puzzle_data'   => $data,
                    'answer'        => $answer,
                    'hints'         => $hints,
                ]);
            }
        }
    }
}
