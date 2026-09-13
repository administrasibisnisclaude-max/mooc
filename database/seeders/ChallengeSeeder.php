<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Challenge;
use App\Models\ChallengeLevel;

class ChallengeSeeder extends Seeder
{
    public function run(): void
    {
        $ps = Challenge::create([
            'title'            => 'Problem-Solving Challenge',
            'description'      => 'Uji kemampuan berpikir algoritmik dan pemrogramanmu! Selesaikan masalah logika & kode dari yang mudah hingga sulit.',
            'type'             => 'problem_solving',
            'difficulty'       => 'medium',
            'time_limit'       => 60,
            'points_per_level' => 15,
            'icon'             => '💡',
            'is_active'        => true,
        ]);

        $psLevels = [
            [1, 'Variabel & Operasi', 'Baca kode berikut dan tentukan outputnya.',
             ['type'=>'code_output','code'=>"x = 10\ny = 3\nprint(x % y)",'language'=>'python','options'=>['0','1','3','10'],'answer_index'=>1,'explanation'=>'10 % 3 = 1 (sisa bagi)'],
             '1', ['Gunakan operasi modulo (%)', 'Sisa dari 10 ÷ 3 adalah...']],

            [2, 'Loop Sederhana', 'Berapa kali "Halo" dicetak?',
             ['type'=>'code_output','code'=>"for i in range(1, 5):\n    if i % 2 == 0:\n        print('Halo')",'language'=>'python','options'=>['1','2','3','4'],'answer_index'=>1,'explanation'=>'i=2 dan i=4 adalah bilangan genap dalam range(1,5)'],
             '2', ['range(1,5) menghasilkan 1,2,3,4', 'Filter: hanya angka genap']],

            [3, 'Fungsi Rekursif', 'Apa hasil dari fungsi ini dengan input 4?',
             ['type'=>'code_output','code'=>"def f(n):\n    if n <= 1:\n        return 1\n    return n * f(n-1)\n\nprint(f(4))",'language'=>'python','options'=>['4','12','24','120'],'answer_index'=>2,'explanation'=>'f(4) = 4×3×2×1 = 24 (faktorial)'],
             '24', ['Ini adalah fungsi faktorial', '4! = 4 × 3 × 2 × 1']],

            [4, 'Array & Index', 'Apa output kode ini?',
             ['type'=>'code_output','code'=>"arr = [10, 20, 30, 40, 50]\nprint(arr[-2])",'language'=>'python','options'=>['20','30','40','50'],'answer_index'=>2,'explanation'=>'Index -2 berarti elemen kedua dari belakang, yaitu 40'],
             '40', ['Index negatif dimulai dari belakang', '-1 adalah elemen terakhir']],

            [5, 'Kompleksitas Waktu', 'Algoritma manakah yang memiliki kompleksitas O(log n)?',
             ['type'=>'multiple_choice','question'=>'Algoritma mana yang paling efisien untuk mencari data dalam array yang sudah terurut?','options'=>['Linear Search','Binary Search','Bubble Sort','Insertion Sort'],'answer_index'=>1,'explanation'=>'Binary Search membelah array menjadi dua setiap iterasi, menghasilkan O(log n)'],
             'Binary Search', ['Algoritma ini membagi data menjadi 2 setiap langkah', 'Cocok untuk data yang sudah terurut']],

            [6, 'String Manipulation', 'Apa hasil dari kode ini?',
             ['type'=>'code_output','code'=>'s = "Hello, World!"\nprint(s[7:12])','language'=>'python','options'=>['World','World!','ello,','Hello'],'answer_index'=>0,'explanation'=>'s[7:12] mengambil karakter dari index 7 (W) hingga 11 (d), yaitu "World"'],
             'World', ['Slicing string: s[start:end]', 'Index dimulai dari 0']],

            [7, 'Dictionary', 'Apa output kode berikut?',
             ['type'=>'code_output','code'=>"d = {'a': 1, 'b': 2, 'c': 3}\nprint(sum(d.values()))",'language'=>'python','options'=>['3','6','abc','Error'],'answer_index'=>1,'explanation'=>'d.values() mengembalikan [1,2,3] dan sum([1,2,3]) = 6'],
             '6', ['d.values() mengambil semua nilai', 'sum() menjumlahkan semua elemen']],

            [8, 'Stack vs Queue', 'Struktur data mana yang menggunakan prinsip LIFO?',
             ['type'=>'multiple_choice','question'=>'Pilih struktur data yang bekerja dengan prinsip "Last In, First Out"','options'=>['Queue','Stack','Linked List','Binary Tree'],'answer_index'=>1,'explanation'=>'Stack (tumpukan) bekerja seperti tumpukan piring: yang terakhir dimasukkan, pertama dikeluarkan (LIFO)'],
             'Stack', ['Bayangkan tumpukan piring', 'LIFO = Last In, First Out']],

            [9, 'Big O Notation', 'Loop bersarang dengan n iterasi masing-masing memiliki kompleksitas?',
             ['type'=>'multiple_choice','question'=>"for i in range(n):\n    for j in range(n):\n        print(i, j)\n\nKompleksitas waktu kode di atas adalah?",'options'=>['O(1)','O(n)','O(n²)','O(log n)'],'answer_index'=>2,'explanation'=>'Dua loop bersarang masing-masing n iterasi menghasilkan n × n = n² operasi'],
             'O(n²)', ['Hitung total iterasi', 'n × n = ?']],

            [10, 'Bug Hunter', 'Temukan bug dalam kode ini!',
             ['type'=>'code_debug','code'=>"def hitung_rata(nums):\n    total = 0\n    for n in nums:\n        total += n\n    return total / len(nums)\n\nprint(hitung_rata([]))",'language'=>'python','bug'=>'Division by zero saat list kosong','options'=>['Tidak ada bug','ZeroDivisionError karena list kosong','SyntaxError','TypeError'],'answer_index'=>1,'explanation'=>'len([]) = 0, sehingga total/0 menyebabkan ZeroDivisionError. Perlu validasi: if not nums: return 0'],
             'ZeroDivisionError karena list kosong', ['Apa yang terjadi saat membagi dengan 0?', 'Perhatikan input list kosong']],
        ];

        foreach ($psLevels as [$num, $title, $instructions, $data, $answer, $hints]) {
            ChallengeLevel::create([
                'challenge_id'  => $ps->id,
                'level_number'  => $num,
                'title'         => $title,
                'instructions'  => $instructions,
                'puzzle_data'   => $data,
                'answer'        => $answer,
                'hints'         => $hints,
            ]);
        }

        $pl = Challenge::create([
            'title'            => 'Puzzle & Logic Challenge',
            'description'      => 'Asah kemampuan berpikir logis dan kreatif! Selesaikan teka-teki angka, pola, anagram, dan logika.',
            'type'             => 'puzzle_logic',
            'difficulty'       => 'easy',
            'time_limit'       => 45,
            'points_per_level' => 10,
            'icon'             => '🧩',
            'is_active'        => true,
        ]);

        $plLevels = [
            [1, 'Deret Angka #1', 'Temukan angka berikutnya dalam deret ini!',
             ['type'=>'number_sequence','sequence'=>[2,4,8,16,null],'blank_index'=>4,'pattern'=>'×2','options'=>['24','32','28','36'],'answer_index'=>1,'explanation'=>'Setiap angka dikali 2: 2→4→8→16→32'],
             '32', ['Lihat hubungan antar angka', 'Apakah dikalikan atau dijumlah?']],

            [2, 'Anagram', 'Susun ulang huruf-huruf ini menjadi sebuah kata!',
             ['type'=>'anagram','scrambled'=>'IMUAK','answer_word'=>'MUSIK','hint_char'=>'M','options'=>['MINUM','MUSIK','MANIK','MIMIK'],'answer_index'=>1,'explanation'=>'Huruf M-U-S-I-K membentuk kata MUSIK'],
             'MUSIK', ['Mulai dari huruf M', 'Berhubungan dengan bunyi/suara']],

            [3, 'Pola Visual', 'Gambar mana yang melengkapi pola?',
             ['type'=>'visual_pattern','description'=>'Lihat pola bentuk berikut dan pilih yang melanjutkan:','sequence'=>['🔴','🔵','🔴','🔵','❓'],'options'=>['🔴','🔵','🟢','🟡'],'answer_index'=>0,'explanation'=>'Pola berulang: Merah → Biru → Merah → Biru → Merah'],
             '0', ['Lihat pola yang berulang', 'Setelah Biru, apa yang datang?']],

            [4, 'Deret Angka #2', 'Angka berapa yang hilang?',
             ['type'=>'number_sequence','sequence'=>[1,1,2,3,5,null,13],'blank_index'=>5,'pattern'=>'Fibonacci','options'=>['7','8','9','11'],'answer_index'=>1,'explanation'=>'Ini adalah deret Fibonacci: setiap angka = jumlah dua angka sebelumnya. 3+5=8'],
             '8', ['Ini deret terkenal dalam matematika', 'Jumlahkan dua angka sebelumnya']],

            [5, 'Teka-Teki Kata', 'Saya punya tangan tapi tidak bisa bertepuk. Saya adalah?',
             ['type'=>'riddle','riddle'=>'Saya punya tangan tapi tidak bisa bertepuk. Saya punya muka tapi tidak bisa tersenyum. Apa saya?','options'=>['Patung','Jam','Boneka','Cermin'],'answer_index'=>1,'explanation'=>'Jam memiliki jarum (tangan) dan muka jam, tapi tidak bisa bergerak seperti manusia'],
             'Jam', ['Benda ini ada di dinding atau di tangan', 'Mengukur sesuatu yang berharga']],

            [6, 'Matematika Cepat', 'Hitung nilai ekspresi berikut!',
             ['type'=>'math','expression'=>'(15 + 5) × 3 - 10 ÷ 2','steps'=>['15+5=20','20×3=60','10÷2=5','60-5=55'],'options'=>['50','55','60','45'],'answer_index'=>1,'explanation'=>'Urutan operasi: kurung dulu → 20×3=60, lalu 10÷2=5, terakhir 60-5=55'],
             '55', ['Ingat aturan BODMAS/PEMDAS', 'Kerjakan dalam kurung dulu']],

            [7, 'Logika Grid', 'Ani tidak suka Merah. Budi tidak suka Biru. Citra tidak suka Hijau. Siapa yang suka Merah?',
             ['type'=>'logic_grid','clues'=>['Ani tidak suka Merah','Budi tidak suka Biru','Citra tidak suka Hijau'],'people'=>['Ani','Budi','Citra'],'colors'=>['Merah','Biru','Hijau'],'options'=>['Ani','Budi','Citra'],'answer_index'=>1,'explanation'=>'Eliminasi: Ani ≠ Merah, Citra ≠ Hijau, Budi ≠ Biru. Jika Budi=Merah, Citra=Biru, Ani=Hijau ✓'],
             'Budi', ['Gunakan eliminasi', 'Mulai dari petunjuk yang paling spesifik']],

            [8, 'Deret Angka #3', 'Angka berapa selanjutnya?',
             ['type'=>'number_sequence','sequence'=>[3,6,11,18,27,null],'blank_index'=>5,'pattern'=>'+3,+5,+7,+9,+11','options'=>['36','37','38','39'],'answer_index'=>2,'explanation'=>'Selisih bertambah: +3,+5,+7,+9,+11,+13. Jadi 27+11=38'],
             '38', ['Perhatikan selisih antar angka', 'Apakah selisihnya bertambah?']],

            [9, 'Anagram Sains', 'Kata apa yang tersembunyi?',
             ['type'=>'anagram','scrambled'=>'IGRENE','answer_word'=>'ENERGI','hint_char'=>'E','options'=>['ENGERI','ENERGI','GENERI','REGING'],'answer_index'=>1,'explanation'=>'Huruf E-N-E-R-G-I disusun ulang menjadi ENERGI'],
             'ENERGI', ['Konsep penting dalam fisika', 'Kemampuan untuk melakukan kerja']],

            [10, 'Master Logic', 'Keluarga punya 5 anak. Setengah dari mereka perempuan. Mungkinkah ini?',
             ['type'=>'riddle','riddle'=>'Sebuah keluarga memiliki 5 anak. Setengah dari jumlah anak adalah perempuan. Apakah ini mungkin?','options'=>['Tidak mungkin, 5 tidak bisa dibagi 2','Mungkin, semua anak perempuan','Mungkin, karena 2.5 tidak bulat','Mungkin, karena semua anak ADALAH perempuan'],'answer_index'=>3,'explanation'=>'Jika SEMUA 5 anak adalah perempuan, maka semua dari mereka perempuan. Jawaban kreatif!'],
             'Mungkin, karena semua anak ADALAH perempuan', ['Berpikir di luar kotak!', 'Siapa bilang setengah harus pecahan?']],
        ];

        foreach ($plLevels as [$num, $title, $instructions, $data, $answer, $hints]) {
            ChallengeLevel::create([
                'challenge_id'  => $pl->id,
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
