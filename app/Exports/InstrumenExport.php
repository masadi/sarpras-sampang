<?php

namespace App\Exports;

use App\Sekolah;
use App\Bangunan;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;

class InstrumenExport implements FromView, WithEvents
{
    use Exportable, RegistersEventListeners;
    private $sekolah_id;
    private $sekolah;
    public function __construct($sekolah_id) 
    {
        $this->sekolah_id = $sekolah_id;
        $this->sekolah = NULL;
    }
    public function view(): View
    {
        $sekolah = Sekolah::find($this->sekolah_id);
        $this->sekolah = $sekolah;
        return view('exports.instrumen', [
            'sekolah' => $sekolah,
        ]);
    }
    public static function afterSheet(AfterSheet $event)
    {
        $sekolah = $event->getConcernable()->sekolah;
        $bangunan = Bangunan::whereHas('tanah', function($query) use ($sekolah){
            $query->where('sekolah_id', $sekolah->sekolah_id);
        })->orderBy('nama')->get();
        if($bangunan->count()){
            $nama_bangunan = $bangunan->implode('nama', ', ');
        } else {
            $nama_bangunan = "Belum ada bangunan";
        }
        $kepemilikan = collect(['Milik', 'Sewa', 'Pinjam', 'Bukan Milik', 'Lainnya']);
        $kepemilikan = $kepemilikan->implode(',');
        //1/250L
        $data_pondasi = collect([
            'Tidak ada kerusakan',
            'Penurunan merata pada seluruh struktur bangunan',
            'Penurunan <= 1/250L',
            'Penurunan > 1/250L',
            'Bangunan miring',
            'Pondasi patah',
        ]);
        $data_pondasi = $data_pondasi->implode(',');
        $tutup_atap = 'BUKAN DAK,DAK BETON,TIDAK MEMILIKI ATAP';
        $sheet = $event->sheet;
        $sheet->setCellValue('A2', "Pilih Bangunan");
        //$nama_bangunan = "DUS800, DUG900+3xRRUS, DUW2100, 2xMU, SIU, DUS800+3xRRUS, DUG900+3xRRUS, DUW2100";
        $cellA2 = $sheet->getCell('A2')->getDataValidation();
        $cellA2->setType(DataValidation::TYPE_LIST);
        $cellA2->setErrorStyle(DataValidation::STYLE_INFORMATION);
        $cellA2->setAllowBlank(false);
        $cellA2->setShowInputMessage(true);
        $cellA2->setShowErrorMessage(true);
        $cellA2->setShowDropDown(true);
        $cellA2->setErrorTitle('Input error');
        $cellA2->setError('Nama bangunan tidak ditemukan.');
        $cellA2->setPromptTitle('Pilih dari daftar');
        $cellA2->setPrompt('Pilih dari daftar.');
        $cellA2->setFormula1('"' . $nama_bangunan . '"');

        $sheet->setCellValue('F2', "Pilih Kepemilikan");
        $cellF2 = $sheet->getCell('F2')->getDataValidation();
        $cellF2->setType(DataValidation::TYPE_LIST);
        $cellF2->setErrorStyle(DataValidation::STYLE_INFORMATION);
        $cellF2->setAllowBlank(false);
        $cellF2->setShowInputMessage(true);
        $cellF2->setShowErrorMessage(true);
        $cellF2->setShowDropDown(true);
        $cellF2->setErrorTitle('Input error');
        $cellF2->setError('Data kepemilikan tidak ditemukan.');
        $cellF2->setPromptTitle('Pilih dari daftar');
        $cellF2->setPrompt('Pilih dari daftar.');
        $cellF2->setFormula1('"' . $kepemilikan . '"');

        $sheet->setCellValue('F3', "Pilih Jenis Kerusakan");
        $cellF3 = $sheet->getCell('F3')->getDataValidation();
        $cellF3->setType(DataValidation::TYPE_LIST);
        $cellF3->setErrorStyle(DataValidation::STYLE_INFORMATION);
        $cellF3->setAllowBlank(false);
        $cellF3->setShowInputMessage(true);
        $cellF3->setShowErrorMessage(true);
        $cellF3->setShowDropDown(true);
        $cellF3->setErrorTitle('Input error');
        $cellF3->setError('Nama kerusakan tidak ditemukan.');
        $cellF3->setPromptTitle('Pilih dari daftar');
        $cellF3->setPrompt('Pilih dari daftar.');
        $cellF3->setFormula1('"' . $data_pondasi . '"');
        //dd($cellF3);
        $sheet->setCellValue('A3', "Kerusakan Pondasi (%)");
        $sheet->setCellValue('A4', "Kerusakan Kolom (%)");
        $sheet->setCellValue('A5', "Kerusakan Balok (%)");
        $sheet->setCellValue('A6', "Kerusakan Pelat Lantai (%)");
        $sheet->setCellValue('A7', "Kerusakan Atap (%)");
        $sheet->setCellValue('A8', "Penutup Atap");

        $sheet->setCellValue('E3', "Keterangan");
        $sheet->setCellValue('E4', "Keterangan");
        $sheet->setCellValue('E5', "Keterangan");
        $sheet->setCellValue('E6', "Keterangan");
        $sheet->setCellValue('E7', "Keterangan");

        $sheet->setCellValue('B8', "Pilih Penutup Atap");
        $cellB8 = $sheet->getCell('B8')->getDataValidation();
        $cellB8->setType(DataValidation::TYPE_LIST);
        $cellB8->setErrorStyle(DataValidation::STYLE_INFORMATION);
        $cellB8->setAllowBlank(false);
        $cellB8->setShowInputMessage(true);
        $cellB8->setShowErrorMessage(true);
        $cellB8->setShowDropDown(true);
        $cellB8->setErrorTitle('Input error');
        $cellB8->setError('Nama Penutup Atap tidak ditemukan.');
        $cellB8->setPromptTitle('Pilih dari daftar');
        $cellB8->setPrompt('Pilih dari daftar.');
        $cellB8->setFormula1('"' . $tutup_atap . '"');
        $sheet->mergeCells('B3:D3');
        $sheet->mergeCells('B4:D4');
        $sheet->mergeCells('B5:D5');
        $sheet->mergeCells('B6:D6');
        $sheet->mergeCells('B7:D7');
        $sheet->mergeCells('B8:H8');
        $sheet->mergeCells('F3:H3');
        $sheet->mergeCells('F4:H4');
        $sheet->mergeCells('F5:H5');
        $sheet->mergeCells('F6:H6');
        $sheet->mergeCells('F7:H7');
        /*$sheet->getStyle("A1:H8")->applyFromArray(
            array(
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                        'color' => array('rgb' => 'DDDDDD')
                    )
                )
            )
        );*/
        $sheet->getStyle('A1:H8')
        ->getBorders()
        ->getAllBorders()
        ->setBorderStyle(Border::BORDER_THIN)
        ->setColor(new Color('000000'));
        $sheet->getColumnDimension('A')->setWidth(50);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(50);
        $sheet->getStyle("A1:H1")->getFont()->setBold( true );
        $sheet->getStyle('A1:H1')->getAlignment()->setHorizontal('center');
    }
}
