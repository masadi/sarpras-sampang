<?php

use Illuminate\Database\Seeder;

class PublisherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('penerbit')->truncate();
        $sql = File::get('database/data/publisher.json');
        $sql = json_decode($sql);
        foreach($sql as $data){
            DB::table('penerbit')->updateOrInsert(
                [
                    'penerbit_id' => $data->id_publisher,
                    'nama' => $data->name,
                    'alamat' => $data->address,
                    'telepon' => $data->phone,
                    'email' => $data->email,
                    'contact_person' => $data->contact_person,
                    'telepon_cp' => $data->cp_phone,
                    'npwp' => $data->npwp,
                    'siup' => $data->siup,
                ]
            );
        }
        Schema::enableForeignKeyConstraints();
    }
}
