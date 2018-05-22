<?php

    // fungsi untuk melakukan Right Shift dari x sebanyak n dengan ukuran data 32 bits
    function ROR($x, $n, $bits = 32)
    {
        // melakukan operasi exponensial 2 pangkat n
        $mask = pow(2, $n) - 1;
        // melakukan operasi AND antara x dengan mask
        $mask_bits = $x & $mask;

        // mengembalikan nilai right shift
        return ($x >> $n) | ($mask_bits << ($bits - $n));
    }

    // fungsi untuk melakukan Left Shift dari x sebanyak n dengan ukuran data 32 bits    
    function ROL($x, $n, $bits = 32)
    {
        // mengembalikan nilai left shift dengan memanggil fungis dari ROR
        return ROR($x, $bits - $n, $bits);
    }

    // fungsi untuk membuat 4 buah blok register dengan panjang 32 bits setiap bloknya
    function blockConverter($sentence)
    {
        //inisialisasi variable
        $encode = array();
        $res = null;
        $i = 0;

        // perulangan untuk mengubah string masukkan menjadi biner
        for ($i=0; $i < strlen($sentence); $i++) { 
        
            if ($i % 4 == 0 && $i != 0) {
                    
                array_push($encode, $res);
                $res = "";
            }

            //merubah char menjadi decimal
            $charToDecimal = ord($sentence[$i]);
            
            //merubah decimal menjadi binner
            $decimalToBinary = decbin($charToDecimal);

            //melengkapi binner menjadi 8 digit
            if (strlen($decimalToBinary) < 8) {
                    
                $eightBits = str_pad($decimalToBinary, 8, 0, STR_PAD_LEFT);                
            }

            // memasukkan binner setiap karakter kedalam array
            $res = $res . $eightBits;
            array_push($encode, $res);
        }

        // mengembalikan nilai array encode
        return $encode;
        }

    function reverseBlockConverter($block)
    {
        $sentence = "";
        
        for ($i=0; $i < count($block); $i++) { 
            
            //merubah decimal menjadi binner
            $decimalToBinary = decbin($block[$i]);

            //melengkapi binner menjadi 32 digit
            if (strlen($decimalToBinary) < 32) {
                
                $thirtyTwoBits = str_pad($decimalToBinary, 32, 0, STR_PAD_LEFT);            
            }

            // merubah 32 bits binner menjadi char
            for ($j=0; $j < 4; $j++) { 
                
                // mengambil binner tertentu
                $getSpesifikBinary = substr($thirtyTwoBits, $j*8, 8);

                // merubah binner menjadi decimal
                $binerToDecimal = bindec($getSpesifikBinary);

                // merubah decimal menjadi char
                $decimalToChar = chr($binerToDecimal);

                // menambahkan char diakhir string
                $sentence .= $decimalToChar;
            }
        }
        
        // mengembalikan string
        return $sentence;
    }

    // fungsi untuk men-generate key dari masukkan user
    function keySchedule($userKey)
    {
        // inisialisasi
        $r = 12;
        $w = 32;
        // $b = strlen($userKey);
        $modulo = pow(2, $w);

        // perulangan untuk membuat inisialisasi nilai dari array s dengan nilai default 0
        for ($i=0; $i < (2 * $r + 4); $i++) { 
                
                $s[$i] = 0;
        }

        //inisialisasi array s pada index 0 dengan nilai default
        $s[0] = 0xB7E15163;

        // perulangan untuk menginisialisasi nilai dari array s setelah index 0
        for ($i=1; $i < (2 * $r + 4); $i++) { 
            
                $s[$i] = ($s[$i - 1] + 0x9E3779B9) % (2 ** $w);
        }

        //memanggil fungsi blockConverter untuk merubah kunci menjadi 4 buah blok register dengan panjang 32 bits setiap bloknya
        $encode = blockConverter(str_pad($userKey, 16));

        //menghitung banyak jumlah array hasil encode dari blockConverter
        $encodeLenght = count($encode);
        
        // melakukan inisialisasi array l
        for ($i=0; $i < $encodeLenght; $i++) { 
                
                $l[$i] = 0;
        }

        // memasukkan nilai dari hasil encode kedalam array l
        for ($i=1; $i < $encodeLenght + 1; $i++) { 
                
                $l[$encodeLenght - $i] = bindec($encode[$i - 1]);
        }

        // memasukkan nilai maximum antara panjang array encode dengan jumlah iterasi
        $v = max($encodeLenght, 2*$r+4);

        // inisialisasi variable
        $A = 0;
        $B = 0;
        $i = 0;
        $j = 0;

        // proses generate key kedalam array s
        for ($index = 0; $index < $v; $index++) { 
                
                $A = $s[$i] = ROL(($s[$i] + $A + $B) % $modulo, 3, 32);
                $B = $l[$j] = ROL(($l[$j] + $A + $B) % $modulo,  ($A + $B) % 32, 32);
                $i = ($i + 1) % (2 * $r + 4);
                $j = ($j + 1) % $encodeLenght;
        }

        return $s;
    }

    function encrypt($sentence, $s)
    {
        
        $encode = blockConverter($sentence);
        $encodeLenght = count($encode);

        $A = bindec($encode[0]);
        $B = bindec($encode[1]);
        $C = bindec($encode[2]);
        $D = bindec($encode[3]);

        $original = array();
        array_push($original, $A);
        array_push($original, $B);
        array_push($original, $C);
        array_push($original, $D);

        $r = 12;
        $w = 32;
        $logw = 5;
        $modulo = pow(2, $w);

        
    }
?>