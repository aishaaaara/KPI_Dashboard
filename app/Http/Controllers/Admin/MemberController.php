<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\Position;
use App\Models\Team;
use App\Models\EmploymentType;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MembersExport;
use App\Imports\MembersImport;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $selectedTeam = $request->team_id;
        $selectedType = $request->employment_type_id;
        $search = $request->search;

        $members = Member::with([
                'position',
                'team',
                'employmentType'
            ])
    ->when($search, function ($query) use ($search) {

        $query->where(function($q) use ($search){

            $q->where('name', 'like', "%{$search}%")
              ->orWhere('eid', 'like', "%{$search}%");

        });

    })
    ->when($selectedTeam, function ($query) use ($selectedTeam) {

        $query->where('team_id', $selectedTeam);

    })
    ->when($selectedType, function ($query) use ($selectedType) {

        $query->where(
            'employment_type_id',
            $selectedType
        );

    })
    ->latest()
    ->get();

        $positions = Position::all();
        $teams = Team::all();
        $employmentTypes = EmploymentType::all();

        return view(
            'admin.members.index',
            compact(
                'members',
                'positions',
                'teams',
                'employmentTypes',
                'selectedTeam',
                'selectedType'
            )
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'position_id' => 'required',
            'team_id' => 'required',
            'employment_type_id' => 'required',

        ]);
    $lastEid = Member::orderByRaw('CAST(SUBSTRING(eid, 4) AS UNSIGNED) DESC')
        ->value('eid');

    $nextNumber = $lastEid ? (int) substr($lastEid, 3) + 1 : 1;

    Member::create([
        'eid'                => 'EMP' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT),
        'name'               => $request->name,
        'position_id'        => $request->position_id,
        'team_id'            => $request->team_id,
        'employment_type_id' => $request->employment_type_id,
        'join_date'          => $request->join_date,
        'end_date'           => $request->end_date,
    ]);

    return redirect()->to('/admin/members');

    }

   public function update(Request $request, $id)
{
    $member = Member::findOrFail($id);

    $request->validate([
    
        'name' => 'required',
        'position_id' => 'required',
        'team_id' => 'required',
        'employment_type_id' => 'required',

    ]);

    $member->update([

        'name' => $request->name,
        'position_id' => $request->position_id,
        'team_id' => $request->team_id,
        'employment_type_id' => $request->employment_type_id,
        'join_date' => $request->join_date,
        'end_date' => $request->end_date,

    ]);

    return redirect()
        ->to('/admin/members')
        ->with('success', 'Member berhasil diupdate');
}
    public function destroy($id)
{
    $member = Member::findOrFail($id);

    // Ambil user lewat user_id
    $user = \App\Models\User::find($member->user_id);

    if ($user) {
        // Hapus register_request berdasarkan email user
        \App\Models\RegisterRequest::where('email', $user->email)->delete();

        // Nonaktifkan login
        $user->update(['is_active' => 0]);
    }

    $member->delete();

    return redirect()
        ->to('/admin/members')
        ->with('success', 'Member berhasil dihapus');
}

    public function export()
{
    return Excel::download(
        new MembersExport,
        'Data Members Developer.xlsx'
    );
}

    public function import(Request $request)
{
    $request->validate([
        'file' => 'required|mimes:xlsx,xls'
    ]);

    try {
        $import = new MembersImport();
        Excel::import($import, $request->file('file'));

        if ($import->failures()->isNotEmpty()) {
            return redirect()->to('/admin/members')
                ->with('import_error', 'Beberapa data gagal diimport. Pastikan format file sesuai template.');
        }

        return redirect()->to('/admin/members')
            ->with('success', 'Data berhasil diimport');

    } catch (\Exception $e) {
        return redirect()->to('/admin/members')
            ->with('import_error', 'File tidak valid atau format tidak sesuai. Gunakan template yang tersedia.');
    }
}

public function downloadTemplate()
{
    $positions       = \App\Models\Position::all();
    $teams           = \App\Models\Team::all();
    $employmentTypes = \App\Models\EmploymentType::all();

    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

    /*
    |----------------------------------------------------------
    | SHEET 1 — Template isi data
    |----------------------------------------------------------
    */
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Data Member');

    // Header
    $headers = ['name', 'position_id', 'team_id', 'employment_type_id', 'join_date', 'end_date'];
    foreach ($headers as $col => $header) {
        $sheet->setCellValueByColumnAndRow($col + 1, 1, $header);
        $sheet->getColumnDimensionByColumn($col + 1)->setWidth(20);
    }

    // Style header
    $sheet->getStyle('A1:F1')->applyFromArray([
        'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
        'fill'      => ['fillType' => 'solid', 'startColor' => ['rgb' => '2563EB']],
        'alignment' => ['horizontal' => 'center'],
    ]);

    // Contoh data
    $sheet->setCellValue('A2', 'John Doe');
    $sheet->setCellValue('E2', '2024-01-15');
    $sheet->setCellValue('F2', '2025-01-15');

    // Format kolom tanggal sebagai text supaya tidak berubah jadi angka serial
    $sheet->getStyle('E:F')
        ->getNumberFormat()
        ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);

    // Catatan format tanggal
    $sheet->setCellValue('G1', '⚠ Format tanggal: YYYY-MM-DD (contoh: 2024-01-15)');
    $sheet->getStyle('G1')->applyFromArray([
        'font' => ['color' => ['rgb' => 'DC2626'], 'bold' => true],
    ]);
    $sheet->getColumnDimension('G')->setWidth(45);

    /*
    |----------------------------------------------------------
    | SHEET 2 — Referensi (hidden, untuk dropdown)
    |----------------------------------------------------------
    */
    $refSheet = $spreadsheet->createSheet();
    $refSheet->setTitle('Referensi');

    // Position
    $refSheet->setCellValue('A1', 'position_id');
    foreach ($positions as $i => $p) {
        $refSheet->setCellValueByColumnAndRow(1, $i + 2, $p->id . ' - ' . $p->name);
    }

    // Team
    $refSheet->setCellValue('B1', 'team_id');
    foreach ($teams as $i => $t) {
        $refSheet->setCellValueByColumnAndRow(2, $i + 2, $t->id . ' - ' . $t->name);
    }

    // Employment Type
    $refSheet->setCellValue('C1', 'employment_type_id');
    foreach ($employmentTypes as $i => $e) {
        $refSheet->setCellValueByColumnAndRow(3, $i + 2, $e->id . ' - ' . $e->name);
    }

    // Style header referensi
    $refSheet->getStyle('A1:C1')->applyFromArray([
        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
        'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '374151']],
    ]);

    // Dropdown validation untuk position_id (kolom B)
    $positionCount = $positions->count();
    $posValidation = new \PhpOffice\PhpSpreadsheet\Cell\DataValidation();
    $posValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST)
        ->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION)
        ->setAllowBlank(false)
        ->setShowDropDown(false)
        ->setFormula1('Referensi!$A$2:$A$' . ($positionCount + 1));

    // Dropdown validation untuk team_id (kolom C)
    $teamCount = $teams->count();
    $teamValidation = new \PhpOffice\PhpSpreadsheet\Cell\DataValidation();
    $teamValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST)
        ->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION)
        ->setAllowBlank(false)
        ->setShowDropDown(false)
        ->setFormula1('Referensi!$B$2:$B$' . ($teamCount + 1));

    // Dropdown validation untuk employment_type_id (kolom D)
    $typeCount = $employmentTypes->count();
    $typeValidation = new \PhpOffice\PhpSpreadsheet\Cell\DataValidation();
    $typeValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST)
        ->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION)
        ->setAllowBlank(false)
        ->setShowDropDown(false)
        ->setFormula1('Referensi!$C$2:$C$' . ($typeCount + 1));

    // Apply dropdown ke 50 baris data
    for ($row = 2; $row <= 51; $row++) {
        $sheet->getCell('B' . $row)->setDataValidation(clone $posValidation);
        $sheet->getCell('C' . $row)->setDataValidation(clone $teamValidation);
        $sheet->getCell('D' . $row)->setDataValidation(clone $typeValidation);
    }

    // Set active sheet ke Data Member
    $spreadsheet->setActiveSheetIndex(0);

    // Download
    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="Template Import Member.xlsx"');
    header('Cache-Control: max-age=0');

    $writer->save('php://output');
    exit;
}

}