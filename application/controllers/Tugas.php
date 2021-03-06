<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Tugas extends CI_Controller
{

	public $mhs, $user;

	public function __construct()
	{
		parent::__construct();
		if (!$this->ion_auth->logged_in()) {
			redirect('auth');
		}

		$this->load->library(['datatables', 'form_validation']); // Load Library Ignited-Datatables
		$this->load->helper('my');
		// $this->load->model('Master_model', 'master');
		$this->load->model('Topik_model', 'topik');
		$this->load->model('Soal_model', 'soal');
		$this->load->model('Tugas_model', 'tugas');
		$this->load->model('Kuis_model', 'kuis');
		$this->form_validation->set_error_delimiters('', '');
		$this->load->library('unit_test');
		$this->user = $this->ion_auth->user()->row();
		$this->mhs 	= $this->tugas->getIdSiswa($this->user->username);
	}

	public function akses_guru()
	{
		if (!$this->ion_auth->in_group('guru')) {
			show_error('Halaman ini khusus untuk guru untuk membuat Test Online, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
		}
	}

	public function akses_siswa()
	{
		if (!$this->ion_auth->in_group('siswa')) {
			show_error('Halaman ini khusus untuk siswa mengikuti tugas, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
		}
	}

	// untuk mengencode data menjadi json
	public function output_json($data, $encode = true)
	{
		if ($encode) $data = json_encode($data);
		$this->output->set_content_type('application/json')->set_output($data);
	}


	public function json($id = null)
	{
		$this->akses_guru();

		$this->output_json($this->tugas->getDataTugas($id), false);
	}

	public function json_kuis($id = null)
	{
		$this->akses_guru();

		$this->output_json($this->kuis->getDataTugas($id), false);
	}

	// fungsi untuk menampilkan seluruh daftar tugas yang dibuat untuk guru
	public function master()
	{
		$this->akses_guru();
		$user = $this->ion_auth->user()->row();
		$data = [
			'user' => $user,
			'judul'	=> 'Tugas',
			'subjudul' => 'Data Tugas',
			'guru' => $this->tugas->getIdGuru($user->username),
		];
		$this->load->view('_templates/dashboard/_header.php', $data);
		$this->load->view('tugas/data');
		$this->load->view('_templates/dashboard/_footer.php');
	}

	public function master_kuis()
	{
		$this->akses_guru();
		$user = $this->ion_auth->user()->row();
		$data = [
			'user' => $user,
			'judul'	=> 'Kuis',
			'subjudul' => 'Data Kuis',
			'guru' => $this->kuis->getIdGuru($user->username),
		];
		$this->load->view('_templates/dashboard/_header.php', $data);
		$this->load->view('kuis/data');
		$this->load->view('_templates/dashboard/_footer.php');
	}

	// fungsi untuk jquery mengambil soal essay berdasar topik
	public function getSoalByTopic()
	{
		$this->akses_guru();
		$topik = $this->input->get('topik');
		$this->output_json($this->tugas->getSoalEssay($topik));
	}

	// fungsi untuk tampilan tambah tugas
	public function add()
	{
		$this->akses_guru();

		$user = $this->ion_auth->user()->row();

		$data = [
			'user' 		=> $user,
			'judul'		=> 'Tugas',
			'subjudul'	=> 'Tambah Tugas',
			'mapel'	=> $this->soal->getMapelGuru($user->username),
			'guru'		=> $this->tugas->getIdGuru($user->username)
		];
		$data['topik'] = $this->topik->getTopikByMapel($data['mapel']->mapel_id);

		// $data['soal'] = $this->tugas->getSoalEssay();

		$this->load->view('_templates/dashboard/_header.php', $data);
		$this->load->view('tugas/add');
		$this->load->view('_templates/dashboard/_footer.php');
	}

	// fungsi untuk tampilan tambah kuis
	public function add_kuis()
	{
		$this->akses_guru();

		$user = $this->ion_auth->user()->row();

		$data = [
			'user' 		=> $user,
			'judul'		=> 'Kuis',
			'subjudul'	=> 'Tambah Kuis',
			'mapel'	=> $this->soal->getMapelGuru($user->username),
			'guru'		=> $this->kuis->getIdGuru($user->username)
		];
		$data['topik'] = $this->topik->getTopikByMapel($data['mapel']->mapel_id);

		// $data['soal'] = $this->tugas->getSoalEssay();

		$this->load->view('_templates/dashboard/_header.php', $data);
		$this->load->view('kuis/add');
		$this->load->view('_templates/dashboard/_footer.php');
	}

	// fungsi untuk menampilkan tampilan edit tugas
	public function edit($id)
	{
		$this->akses_guru();

		$user = $this->ion_auth->user()->row();

		$data = [
			'user' 		=> $user,
			'judul'		=> 'Tugas',
			'subjudul'	=> 'Edit Tugas',
			'mapel'	=> $this->soal->getMapelGuru($user->username),
			'guru'		=> $this->tugas->getIdGuru($user->username),
			'tugas'		=> $this->tugas->getTugasById($id),
		];
		$data['topik'] = $this->topik->getTopikByMapel($data['mapel']->mapel_id);
		$data['soal'] = $this->tugas->getSoalEssay($data['tugas']->topik_id);

		$this->load->view('_templates/dashboard/_header.php', $data);
		$this->load->view('tugas/edit');
		$this->load->view('_templates/dashboard/_footer.php');
	}

	// fungsi untuk menampilkan tampilan edit kuis
	public function edit_kuis($id)
	{
		$this->akses_guru();

		$user = $this->ion_auth->user()->row();

		$data = [
			'user' 		=> $user,
			'judul'		=> 'Kuis',
			'subjudul'	=> 'Edit Kuis',
			'mapel'		=> $this->soal->getMapelGuru($user->username),
			'guru'		=> $this->kuis->getIdGuru($user->username),
			'tugas'		=> $this->kuis->getTugasById($id),
		];
		$data['topik'] = $this->topik->getTopikByMapel($data['mapel']->mapel_id);
		$data['soal'] = $this->kuis->getSoalEssay($data['tugas']->topik_id);

		$this->load->view('_templates/dashboard/_header.php', $data);
		$this->load->view('kuis/edit');
		$this->load->view('_templates/dashboard/_footer.php');
	}

	public function convert_tgl($tgl)
	{
		$this->akses_guru();
		return date('Y-m-d H:i:s', strtotime($tgl));
	}

	public function validasi($m = null, $t = null)
	{
		$this->akses_guru();

		$jml 	= $this->tugas->getJumlahSoal($m, $t)->jml_soal;
		$jml_a 	= $jml + 1; // Jika tidak mengerti, silahkan baca user_guide codeigniter tentang form_validation pada bagian less_than

		$this->form_validation->set_rules('nama_tugas', 'Nama Tugas/Kuis', 'required|alpha_numeric_spaces|max_length[50]');
		$this->form_validation->set_rules('topik', 'Topik', 'required');
		$this->form_validation->set_rules('jumlah_soal', 'Jumlah Soal', "required|integer|less_than[{$jml_a}]|greater_than[0]", ['less_than' => "Soal tidak cukup, hanya ada {$jml} soal untuk topik ini"]);
		$this->form_validation->set_rules('tgl_mulai', 'Tanggal Mulai', 'required');
		$this->form_validation->set_rules('tgl_selesai', 'Tanggal Selesai', 'required');
		$this->form_validation->set_rules('jenis', 'Acak Soal', 'required|in_list[acak,urut]');
		$this->form_validation->set_rules('jenis_soal', 'Tipe Soal', 'required|in_list[pilgan,essay]');
	}

	public function validasi_essay()
	{
		$this->akses_guru();

		// $jml 	= $this->tugas->getJumlahSoal($m, $t)->jml_soal;
		// $jml_a 	= $jml + 1; // Jika tidak mengerti, silahkan baca user_guide codeigniter tentang form_validation pada bagian less_than

		$this->form_validation->set_rules('nama_tugas', 'Nama Tugas/Kuis', 'required|alpha_numeric_spaces|max_length[50]');
		$this->form_validation->set_rules('topik', 'Topik', 'required');
		$this->form_validation->set_rules('tgl_mulai', 'Tanggal Mulai', 'required');
		$this->form_validation->set_rules('tgl_selesai', 'Tanggal Selesai', 'required');
		$this->form_validation->set_rules('jenis_soal', 'Tipe Soal', 'required|in_list[pilgan,essay]');
	}

	// save tugas
	public function save()
	{
		$m 		= $this->input->post('mapel_id', true);
		$t	 	= $this->input->post('topik', true);
		if ($this->input->post('jenis_soal') == 'pilgan') {
			$this->validasi($m, $t);
		} else {
			$this->validasi_essay();
		}

		$this->load->helper('string');

		$mapel_id 		= $this->input->post('mapel_id', true);
		$topik_id	 	= $this->input->post('topik', true);
		$method 		= $this->input->post('method', true);
		$guru_id 		= $this->input->post('guru_id', true);
		$nama_tugas 	= $this->input->post('nama_tugas', true);
		$jumlah_soal 	= $this->input->post('jumlah_soal', true);
		$tgl_mulai 		= $this->convert_tgl($this->input->post('tgl_mulai', 	true));
		$tgl_selesai	= $this->convert_tgl($this->input->post('tgl_selesai', true));
		$jenis_tugas	= $this->input->post('jenis_tugas', true);
		$jenis_soal		= $this->input->post('jenis_soal', true);
		$jenis			= $this->input->post('jenis', true);
		$id_soal		= $this->input->post('soal', true);
		$token 			= strtoupper(random_string('alpha', 5));

		if ($this->form_validation->run() === FALSE) {
			$data['status'] = false;
			$data['errors'] = [
				'nama_tugas' 	=> form_error('nama_tugas'),
				'topik'		 	=> form_error('topik'),
				'jumlah_soal' 	=> form_error('jumlah_soal'),
				'tgl_mulai' 	=> form_error('tgl_mulai'),
				'tgl_selesai' 	=> form_error('tgl_selesai'),
				'jenis_soal'	=> form_error('jenis_soal'),
				'jenis' 		=> form_error('jenis'),
				'soal' 			=> form_error('soal')
			];
		} else {
			if ($jenis_soal == 'pilgan') {
				$input = [
					'nama_tugas' 	=> $nama_tugas,
					'topik_id'	 	=> $topik_id,
					'jumlah_soal' 	=> $jumlah_soal,
					'tgl_mulai' 	=> $tgl_mulai,
					'terlambat' 	=> $tgl_selesai,
					'jenis_tugas'	=> $jenis_tugas,
					'jenis_soal'	=> $jenis_soal,
					'jenis' 		=> $jenis,
				];
			} else {
				$input = [
					'nama_tugas' 	=> $nama_tugas,
					'topik_id'	 	=> $topik_id,
					'tgl_mulai' 	=> $tgl_mulai,
					'terlambat' 	=> $tgl_selesai,
					'jumlah_soal' 	=> 1,
					'jenis_tugas'	=> $jenis_tugas,
					'jenis_soal'	=> $jenis_soal,
					'id_soal_essay'	=> $id_soal,
				];
			}
			if ($method === 'add') {
				$input['guru_id']	= $guru_id;
				$input['mapel_id'] 	= $mapel_id;
				$input['token']		= $token;
				$action = $this->tugas->create('tugas', $input);
			} else if ($method === 'edit') {
				$id_tugas = $this->input->post('id_tugas', true);
				$action = $this->tugas->update('tugas', $input, 'id_tugas', $id_tugas);
			}
			$data['status'] = $action ? TRUE : FALSE;
		}
		$this->output_json($data);
	}

	public function save_kuis()
	{
		$m 		= $this->input->post('mapel_id', true);
		$t	 	= $this->input->post('topik', true);
		if ($this->input->post('jenis_soal') == 'pilgan') {
			$this->validasi($m, $t);
			$this->form_validation->set_rules('waktu', 'Waktu', 'required|integer|max_length[4]|greater_than[0]');
		} else {
			$this->validasi_essay();
			$this->form_validation->set_rules('waktu', 'Waktu', 'required|integer|max_length[4]|greater_than[0]');
		}

		$this->load->helper('string');

		$mapel_id 		= $this->input->post('mapel_id', true);
		$topik_id	 	= $this->input->post('topik', true);
		$method 		= $this->input->post('method', true);
		$guru_id 		= $this->input->post('guru_id', true);
		$nama_tugas 	= $this->input->post('nama_tugas', true);
		$jumlah_soal 	= $this->input->post('jumlah_soal', true);
		$tgl_mulai 		= $this->convert_tgl($this->input->post('tgl_mulai', 	true));
		$tgl_selesai	= $this->convert_tgl($this->input->post('tgl_selesai', true));
		$waktu			= $this->input->post('waktu', true);
		$jenis_tugas	= $this->input->post('jenis_tugas', true);
		$jenis_soal		= $this->input->post('jenis_soal', true);
		$jenis			= $this->input->post('jenis', true);
		$id_soal		= $this->input->post('soal', true);
		$token 			= strtoupper(random_string('alpha', 5));

		if ($this->form_validation->run() === FALSE) {
			$data['status'] = false;
			$data['errors'] = [
				'nama_tugas' 	=> form_error('nama_tugas'),
				'topik'		 	=> form_error('topik'),
				'jumlah_soal' 	=> form_error('jumlah_soal'),
				'tgl_mulai' 	=> form_error('tgl_mulai'),
				'tgl_selesai' 	=> form_error('tgl_selesai'),
				'waktu' 		=> form_error('waktu'),
				'jenis_soal'	=> form_error('jenis_soal'),
				'jenis' 		=> form_error('jenis'),
				'soal' 			=> form_error('soal')
			];
		} else {
			if ($jenis_soal == 'pilgan') {
				$input = [
					'nama_tugas' 	=> $nama_tugas,
					'topik_id'	 	=> $topik_id,
					'jumlah_soal' 	=> $jumlah_soal,
					'tgl_mulai' 	=> $tgl_mulai,
					'terlambat' 	=> $tgl_selesai,
					'waktu' 		=> $waktu,
					'jenis_tugas'	=> $jenis_tugas,
					'jenis_soal'	=> $jenis_soal,
					'jenis' 		=> $jenis,
				];
			} else {
				$input = [
					'nama_tugas' 	=> $nama_tugas,
					'topik_id'	 	=> $topik_id,
					'tgl_mulai' 	=> $tgl_mulai,
					'terlambat' 	=> $tgl_selesai,
					'waktu' 		=> $waktu,
					'jumlah_soal' 	=> 1,
					'jenis_tugas'	=> $jenis_tugas,
					'jenis_soal'	=> $jenis_soal,
					'id_soal_essay'	=> $id_soal,
				];
			}
			if ($method === 'add') {
				$input['guru_id']	= $guru_id;
				$input['mapel_id'] 	= $mapel_id;
				$input['token']		= $token;
				$action = $this->kuis->create('tugas', $input);
			} else if ($method === 'edit') {
				$id_tugas = $this->input->post('id_tugas', true);
				$action = $this->kuis->update('tugas', $input, 'id_tugas', $id_tugas);
			}
			$data['status'] = $action ? TRUE : FALSE;
		}
		$this->output_json($data);
	}

	// fungsi untuk menghapus tugas dan kuis
	public function delete()
	{
		$this->akses_guru();
		$chk = $this->input->post('checked', true);
		if (!$chk) {
			$this->output_json(['status' => false]);
		} else {
			if ($this->tugas->delete('tugas', $chk, 'id_tugas')) {
				$this->output_json(['status' => true, 'total' => count($chk)]);
			}
		}
	}

	// fungsi untuk memberikan token baru pada tugas dan kuis
	public function refresh_token($id)
	{
		$this->load->helper('string');
		$data['token'] = strtoupper(random_string('alpha', 5));
		$refresh = $this->tugas->update('tugas', $data, 'id_tugas', $id);
		$data['status'] = $refresh ? TRUE : FALSE;
		$this->output_json($data);
	}

	/**==========================================================
	 * BAGIAN SISWA
	 */

	//  fungsi untuk jquery mengambil data tugas
	public function list_json()
	{
		$this->akses_siswa();

		$list = $this->tugas->getListTugas($this->mhs->id_siswa, $this->mhs->kelas_id);
		$this->output_json($list, false);
	}

	//  fungsi untuk jquery mengambil data kuis
	public function list_json_kuis()
	{
		$this->akses_siswa();

		$list = $this->kuis->getListTugas($this->mhs->id_siswa, $this->mhs->kelas_id);
		$this->output_json($list, false);
	}

	// ini fungsi untuk menampilkan daftar tugas di siswa
	public function list()
	{
		$this->akses_siswa();

		$user = $this->ion_auth->user()->row();

		$data = [
			'user' 		=> $user,
			'judul'		=> 'Tugas',
			'subjudul'	=> 'List Tugas',
			'mhs' 		=> $this->tugas->getIdSiswa($user->username),
		];
		$this->load->view('_templates/dashboard/_header.php', $data);
		$this->load->view('tugas/list');
		$this->load->view('_templates/dashboard/_footer.php');
	}

	// ini fungsi untuk menampilkan daftar kuis di siswa
	public function list_kuis()
	{
		$this->akses_siswa();

		$user = $this->ion_auth->user()->row();

		$data = [
			'user' 		=> $user,
			'judul'		=> 'Kuis',
			'subjudul'	=> 'List Kuis',
			'mhs' 		=> $this->kuis->getIdSiswa($user->username),
		];
		$this->load->view('_templates/dashboard/_header.php', $data);
		$this->load->view('kuis/list');
		$this->load->view('_templates/dashboard/_footer.php');
	}

	// ketika menekan tombol ikuti tugas, akan mengakses fungsi token
	public function token($id)
	{
		$this->akses_siswa();
		$user = $this->ion_auth->user()->row();

		$data = [
			'user' 		=> $user,
			'judul'		=> 'Tugas',
			'subjudul'	=> 'Token Tugas',
			'mhs' 		=> $this->tugas->getIdSiswa($user->username),
			'tugas'		=> $this->tugas->getTugasById($id),
			'encrypted_id' => urlencode($this->encryption->encrypt($id))
		];
		$this->load->view('_templates/topnav/_header.php', $data);
		$this->load->view('tugas/token');
		$this->load->view('_templates/topnav/_footer.php');
	}

	// ketika menekan tombol ikuti kuis, akan mengakses fungsi token
	public function token_kuis($id)
	{
		$this->akses_siswa();
		$user = $this->ion_auth->user()->row();

		$data = [
			'user' 		=> $user,
			'judul'		=> 'Kuis',
			'subjudul'	=> 'Token Kuis',
			'mhs' 		=> $this->kuis->getIdSiswa($user->username),
			'tugas'		=> $this->kuis->getTugasById($id),
			'encrypted_id' => urlencode($this->encryption->encrypt($id))
		];
		$this->load->view('_templates/topnav/_header.php', $data);
		$this->load->view('kuis/token');
		$this->load->view('_templates/topnav/_footer.php');
	}

	// untuk memvalidasi inputan token dari siswa
	public function cektoken()
	{
		$id = $this->input->post('id_tugas', true);
		$token = $this->input->post('token', true);
		$cek = $this->tugas->getTugasById($id);

		$data['status'] = $token === $cek->token ? TRUE : FALSE;
		$this->output_json($data);
	}

	public function encrypt()
	{
		$id = $this->input->post('id', true);
		$key = urlencode($this->encryption->encrypt($id));
		// $decrypted = $this->encryption->decrypt(rawurldecode($key));
		$this->output_json(['key' => $key]);
	}

	// fungsi index untuk halaman soal tugas dan jawaban
	public function index()
	{
		$this->akses_siswa();
		$key = $this->input->get('key', true);
		$id  = $this->encryption->decrypt(rawurldecode($key));

		// mengambil data tugas dan soal berdasarkan id
		$tugas 		= $this->tugas->getTugasById($id);
		$soal 		= $this->tugas->getSoal($id);

		$mhs		= $this->mhs;
		$hasil_tugas 	= $this->tugas->HslTugas($id, $mhs->id_siswa);

		$cek_sudah_ikut = $hasil_tugas->num_rows();

		// dicek apakah siswa sudah pernah ambil atau belum
		if ($cek_sudah_ikut < 1) {
			// dicek apakah tugas pilgan atau essay
			if ($tugas->jenis_soal == "pilgan") { //jika pilgan
				$soal_urut_ok 	= array();
				$i = 0;
				foreach ($soal as $s) {
					$soal_per = new stdClass();
					$soal_per->id_soal 		= $s->id_soal;
					$soal_per->soal 		= $s->soal;
					$soal_per->file 		= $s->file;
					$soal_per->tipe_file 	= $s->tipe_file;
					$soal_per->opsi_a 		= $s->opsi_a;
					$soal_per->opsi_b 		= $s->opsi_b;
					$soal_per->opsi_c 		= $s->opsi_c;
					$soal_per->opsi_d 		= $s->opsi_d;
					$soal_per->opsi_e 		= $s->opsi_e;
					$soal_per->jawaban 		= $s->jawaban;
					$soal_urut_ok[$i] 		= $soal_per;
					$i++;
				}
				$soal_urut_ok 	= $soal_urut_ok;
				$list_id_soal	= "";
				$list_jw_soal 	= "";
				if (!empty($soal)) {
					foreach ($soal as $d) {
						$list_id_soal .= $d->id_soal . ",";
						$list_jw_soal .= $d->id_soal . "::N,";
					}
				}
				$list_id_soal 	= substr($list_id_soal, 0, -1);
				$list_jw_soal 	= substr($list_jw_soal, 0, -1);
				$waktu_selesai 	= date('Y-m-d H:i:s', strtotime("+{$tugas->waktu} minute"));
				$time_mulai		= date('Y-m-d H:i:s');

				$input = [
					'tugas_id' 		=> $id,
					'siswa_id'		=> $mhs->id_siswa,
					'jenis_tugas'	=> $tugas->jenis_tugas,
					'list_soal'		=> $list_id_soal,
					'list_jawaban' 	=> $list_jw_soal,
					'jml_benar'		=> 0,
					'nilai'			=> 0,
					'nilai_bobot'	=> 0,
					'tgl_mulai'		=> $time_mulai,
					'tgl_selesai'	=> $waktu_selesai,
					'status'		=> 'Y'
				];
			} else { //jika essay
				$list_id_soal 	= $soal->soal;
				$list_jw_soal 	= "";
				$waktu_selesai 	= date('Y-m-d H:i:s', strtotime("+{$tugas->waktu} minute"));
				$time_mulai		= date('Y-m-d H:i:s');

				$input = [
					'tugas_id' 		=> $id,
					'siswa_id'		=> $mhs->id_siswa,
					'jenis_tugas'	=> $tugas->jenis_tugas,
					'jenis_soal'	=> $tugas->jenis_soal,
					'list_soal'		=> $list_id_soal,
					'list_jawaban' 	=> $list_jw_soal,
					'jml_benar'		=> 0,
					'nilai'			=> 0,
					'nilai_bobot'	=> 0,
					'tgl_mulai'		=> $time_mulai,
					'tgl_selesai'	=> $waktu_selesai,
					'status'		=> 'Y'
				];
			}
			$this->tugas->create('hasil_tugas', $input);

			// Setelah insert wajib refresh dulu
			redirect('tugas/?key=' . urlencode($key), 'location', 301);
		}

		if ($tugas->jenis_soal == 'pilgan') {
			$q_soal = $hasil_tugas->row();

			$urut_soal 		= explode(",", $q_soal->list_jawaban);
			$soal_urut_ok	= array();
			for ($i = 0; $i < sizeof($urut_soal); $i++) {
				$pc_urut_soal	= explode(":", $urut_soal[$i]);
				$pc_urut_soal1 	= empty($pc_urut_soal[1]) ? "''" : "'{$pc_urut_soal[1]}'";
				$ambil_soal 	= $this->tugas->ambilSoal($pc_urut_soal1, $pc_urut_soal[0]);
				$soal_urut_ok[] = $ambil_soal;
			}

			$detail_tes = $q_soal;
			$soal_urut_ok = $soal_urut_ok;

			$pc_list_jawaban = explode(",", $detail_tes->list_jawaban);
			$arr_jawab = array();
			foreach ($pc_list_jawaban as $v) {
				$pc_v 	= explode(":", $v);
				$idx 	= $pc_v[0];
				$val 	= $pc_v[1];
				$rg 	= $pc_v[2];

				$arr_jawab[$idx] = array("j" => $val, "r" => $rg);
			}

			$arr_opsi = array("a", "b", "c", "d", "e");
			$html = '';
			$no = 1;
			if (!empty($soal_urut_ok)) {
				foreach ($soal_urut_ok as $s) {
					$path = 'uploads/bank_soal/';
					$vrg = $arr_jawab[$s->id_soal]["r"] == "" ? "N" : $arr_jawab[$s->id_soal]["r"];
					$html .= '<input type="hidden" name="id_soal_' . $no . '" value="' . $s->id_soal . '">';
					$html .= '<input type="hidden" id="jenis_soal" name="jenis_soal" value="' . $s->jenis_soal . '">';
					$html .= '<input type="hidden" name="rg_' . $no . '" id="rg_' . $no . '" value="' . $vrg . '">';
					$html .= '<div class="step" id="widget_' . $no . '">';

					$html .= '<div class="text-center"><div class="w-25">' . tampil_media($path . $s->file) . '</div></div>' . $s->soal . '<div class="funkyradio">';
					for ($j = 0; $j < $this->config->item('jml_opsi'); $j++) {
						$opsi 			= "opsi_" . $arr_opsi[$j];
						$file 			= "file_" . $arr_opsi[$j];
						$checked 		= $arr_jawab[$s->id_soal]["j"] == strtoupper($arr_opsi[$j]) ? "checked" : "";
						$pilihan_opsi 	= !empty($s->$opsi) ? $s->$opsi : "";
						$tampil_media_opsi = (is_file(base_url() . $path . $s->$file) || $s->$file != "") ? tampil_media($path . $s->$file) : "";
						$html .= '<div class="funkyradio-success" onclick="return simpan_sementara();">
							<input type="radio" id="opsi_' . strtolower($arr_opsi[$j]) . '_' . $s->id_soal . '" name="opsi_' . $no . '" value="' . strtoupper($arr_opsi[$j]) . '" ' . $checked . '> <label for="opsi_' . strtolower($arr_opsi[$j]) . '_' . $s->id_soal . '"><div class="huruf_opsi">' . $arr_opsi[$j] . '</div> <p>' . $pilihan_opsi . '</p><div class="w-25">' . $tampil_media_opsi . '</div></label></div>';
					}
					$html .= '</div></div>';
					$no++;
				}
			}
			// Enkripsi Id Tes
			$id_tes = $this->encryption->encrypt($detail_tes->id);
			$data = [
				'jenis_soal' => $tugas->jenis_soal,
				'user' 		=> $this->user,
				'mhs'		=> $this->mhs,
				'judul'		=> 'Tugas',
				'subjudul'	=> 'Lembar Tugas',
				'soal'		=> $detail_tes,
				'no' 		=> $no,
				'html' 		=> $html,
				'id_tes'	=> $id_tes
			];
			$this->load->view('_templates/topnav/_header.php', $data);
			$this->load->view('tugas/sheet');
			$this->load->view('_templates/topnav/_footer.php');
		} else {

			// menyimpan id hasil tugas
			$detail_tes = $hasil_tugas->row();

			$html = '';
			$no = 1;

			$path = 'uploads/bank_soal/';
			$html .= '<input type="hidden" id="jenis_soal" name="jenis_soal" value="' . $detail_tes->jenis_soal . '">';
			$html .= '<div class="step" id="widget_' . $no . '">';

			$html .= '<div class="text-center"><div class="w-25">' . tampil_media($path . $soal->file) . '</div></div>' . $soal->soal;
			$html .= '<textarea class="froala" id="jawaban">' . $detail_tes->list_jawaban . '</textarea>';
			$html .= '</div>';
			$no++;

			// Enkripsi Id Tes
			$id_tes = $this->encryption->encrypt($detail_tes->id);
			$data = [
				'jenis_soal' => $tugas->jenis_soal,
				'user' 		=> $this->user,
				'mhs'		=> $this->mhs,
				'judul'		=> 'Tugas',
				'subjudul'	=> 'Lembar Tugas',
				'soal'		=> $detail_tes,
				'no' 		=> $no,
				'html' 		=> $html,
				'id_tes'	=> $id_tes
			];
			$this->load->view('_templates/topnav/_header.php', $data);
			$this->load->view('tugas/sheet');
			$this->load->view('_templates/topnav/_footer.php');
		}
	}

	// fungsi index untuk halaman soal tugas dan jawaban
	public function index_kuis()
	{
		$this->akses_siswa();
		$key = $this->input->get('key', true);
		$id  = $this->encryption->decrypt(rawurldecode($key));

		// mengambil data kuis dan soal berdasarkan id
		$tugas 		= $this->kuis->getTugasById($id);
		$soal 		= $this->kuis->getSoal($id);

		$mhs		= $this->mhs;
		$hasil_tugas 	= $this->kuis->HslTugas($id, $mhs->id_siswa);

		$cek_sudah_ikut = $hasil_tugas->num_rows();

		// dicek apakah siswa sudah pernah ambil atau belum
		if ($cek_sudah_ikut < 1) {
			// dicek apakah kuis pilgan atau essay
			if ($tugas->jenis_soal == "pilgan") { //jika pilgan
				$soal_urut_ok 	= array();
				$i = 0;
				foreach ($soal as $s) {
					$soal_per = new stdClass();
					$soal_per->id_soal 		= $s->id_soal;
					$soal_per->soal 		= $s->soal;
					$soal_per->file 		= $s->file;
					$soal_per->tipe_file 	= $s->tipe_file;
					$soal_per->opsi_a 		= $s->opsi_a;
					$soal_per->opsi_b 		= $s->opsi_b;
					$soal_per->opsi_c 		= $s->opsi_c;
					$soal_per->opsi_d 		= $s->opsi_d;
					$soal_per->opsi_e 		= $s->opsi_e;
					$soal_per->jawaban 		= $s->jawaban;
					$soal_urut_ok[$i] 		= $soal_per;
					$i++;
				}
				$soal_urut_ok 	= $soal_urut_ok;
				$list_id_soal	= "";
				$list_jw_soal 	= "";
				if (!empty($soal)) {
					foreach ($soal as $d) {
						$list_id_soal .= $d->id_soal . ",";
						$list_jw_soal .= $d->id_soal . "::N,";
					}
				}
				$list_id_soal 	= substr($list_id_soal, 0, -1);
				$list_jw_soal 	= substr($list_jw_soal, 0, -1);
				$waktu_selesai 	= date('Y-m-d H:i:s', strtotime("+{$tugas->waktu} minute"));
				$time_mulai		= date('Y-m-d H:i:s');

				$input = [
					'tugas_id' 		=> $id,
					'siswa_id'		=> $mhs->id_siswa,
					'jenis_tugas'	=> $tugas->jenis_tugas,
					'list_soal'		=> $list_id_soal,
					'list_jawaban' 	=> $list_jw_soal,
					'jml_benar'		=> 0,
					'nilai'			=> 0,
					'nilai_bobot'	=> 0,
					'tgl_mulai'		=> $time_mulai,
					'tgl_selesai'	=> $waktu_selesai,
					'status'		=> 'Y'
				];
			} else { //jika essay
				$list_id_soal 	= $soal->soal;
				$list_jw_soal 	= "";
				$waktu_selesai 	= date('Y-m-d H:i:s', strtotime("+{$tugas->waktu} minute"));
				$time_mulai		= date('Y-m-d H:i:s');

				$input = [
					'tugas_id' 		=> $id,
					'siswa_id'		=> $mhs->id_siswa,
					'jenis_tugas'	=> $tugas->jenis_tugas,
					'jenis_soal'	=> $tugas->jenis_soal,
					'list_soal'		=> $list_id_soal,
					'list_jawaban' 	=> $list_jw_soal,
					'jml_benar'		=> 0,
					'nilai'			=> 0,
					'nilai_bobot'	=> 0,
					'tgl_mulai'		=> $time_mulai,
					'tgl_selesai'	=> $waktu_selesai,
					'status'		=> 'Y'
				];
			}
			$this->kuis->create('hasil_tugas', $input);

			// Setelah insert wajib refresh dulu
			redirect('kuis/?key=' . urlencode($key), 'location', 301);
		}

		if ($tugas->jenis_soal == 'pilgan') {
			$q_soal = $hasil_tugas->row();

			$urut_soal 		= explode(",", $q_soal->list_jawaban);
			$soal_urut_ok	= array();
			for ($i = 0; $i < sizeof($urut_soal); $i++) {
				$pc_urut_soal	= explode(":", $urut_soal[$i]);
				$pc_urut_soal1 	= empty($pc_urut_soal[1]) ? "''" : "'{$pc_urut_soal[1]}'";
				$ambil_soal 	= $this->kuis->ambilSoal($pc_urut_soal1, $pc_urut_soal[0]);
				$soal_urut_ok[] = $ambil_soal;
			}

			$detail_tes = $q_soal;
			$soal_urut_ok = $soal_urut_ok;

			$pc_list_jawaban = explode(",", $detail_tes->list_jawaban);
			$arr_jawab = array();
			foreach ($pc_list_jawaban as $v) {
				$pc_v 	= explode(":", $v);
				$idx 	= $pc_v[0];
				$val 	= $pc_v[1];
				$rg 	= $pc_v[2];

				$arr_jawab[$idx] = array("j" => $val, "r" => $rg);
			}

			$arr_opsi = array("a", "b", "c", "d", "e");
			$html = '';
			$no = 1;
			if (!empty($soal_urut_ok)) {
				foreach ($soal_urut_ok as $s) {
					$path = 'uploads/bank_soal/';
					$vrg = $arr_jawab[$s->id_soal]["r"] == "" ? "N" : $arr_jawab[$s->id_soal]["r"];
					$html .= '<input type="hidden" name="id_soal_' . $no . '" value="' . $s->id_soal . '">';
					$html .= '<input type="hidden" id="jenis_soal" name="jenis_soal" value="' . $s->jenis_soal . '">';
					$html .= '<input type="hidden" name="rg_' . $no . '" id="rg_' . $no . '" value="' . $vrg . '">';
					$html .= '<div class="step" id="widget_' . $no . '">';

					$html .= '<div class="text-center"><div class="w-25">' . tampil_media($path . $s->file) . '</div></div>' . $s->soal . '<div class="funkyradio">';
					for ($j = 0; $j < $this->config->item('jml_opsi'); $j++) {
						$opsi 			= "opsi_" . $arr_opsi[$j];
						$file 			= "file_" . $arr_opsi[$j];
						$checked 		= $arr_jawab[$s->id_soal]["j"] == strtoupper($arr_opsi[$j]) ? "checked" : "";
						$pilihan_opsi 	= !empty($s->$opsi) ? $s->$opsi : "";
						$tampil_media_opsi = (is_file(base_url() . $path . $s->$file) || $s->$file != "") ? tampil_media($path . $s->$file) : "";
						$html .= '<div class="funkyradio-success" onclick="return simpan_sementara();">
							<input type="radio" id="opsi_' . strtolower($arr_opsi[$j]) . '_' . $s->id_soal . '" name="opsi_' . $no . '" value="' . strtoupper($arr_opsi[$j]) . '" ' . $checked . '> <label for="opsi_' . strtolower($arr_opsi[$j]) . '_' . $s->id_soal . '"><div class="huruf_opsi">' . $arr_opsi[$j] . '</div> <p>' . $pilihan_opsi . '</p><div class="w-25">' . $tampil_media_opsi . '</div></label></div>';
					}
					$html .= '</div></div>';
					$no++;
				}
			}
			// Enkripsi Id Tes
			$id_tes = $this->encryption->encrypt($detail_tes->id);
			$data = [
				'jenis_soal' => $tugas->jenis_soal,
				'user' 		=> $this->user,
				'mhs'		=> $this->mhs,
				'judul'		=> 'Kuis',
				'subjudul'	=> 'Lembar Kuis',
				'soal'		=> $detail_tes,
				'no' 		=> $no,
				'html' 		=> $html,
				'id_tes'	=> $id_tes
			];
			$this->load->view('_templates/topnav/_header.php', $data);
			$this->load->view('kuis/sheet');
			$this->load->view('_templates/topnav/_footer.php');
		} else {

			// menyimpan id hasil kuis
			$detail_tes = $hasil_tugas->row();

			$html = '';
			$no = 1;

			$path = 'uploads/bank_soal/';
			$html .= '<input type="hidden" id="jenis_soal" name="jenis_soal" value="' . $detail_tes->jenis_soal . '">';
			$html .= '<div class="step" id="widget_' . $no . '">';

			$html .= '<div class="text-center"><div class="w-25">' . tampil_media($path . $soal->file) . '</div></div>' . $soal->soal;
			$html .= '<textarea class="froala" id="jawaban">' . $detail_tes->list_jawaban . '</textarea>';
			$html .= '</div>';
			$no++;

			// Enkripsi Id Tes
			$id_tes = $this->encryption->encrypt($detail_tes->id);
			$data = [
				'jenis_soal' => $tugas->jenis_soal,
				'user' 		=> $this->user,
				'mhs'		=> $this->mhs,
				'judul'		=> 'Kuis',
				'subjudul'	=> 'Lembar Kuis',
				'soal'		=> $detail_tes,
				'no' 		=> $no,
				'html' 		=> $html,
				'id_tes'	=> $id_tes
			];
			$this->load->view('_templates/topnav/_header.php', $data);
			$this->load->view('kuis/sheet');
			$this->load->view('_templates/topnav/_footer.php');
		}
	}

	public function simpan_satu()
	{
		// Decrypt Id
		$id_tes = $this->input->post('id', true);
		$id_tes = $this->encryption->decrypt($id_tes);

		$input 	= $this->input->post(null, true);
		$list_jawaban 	= "";
		for ($i = 1; $i < $input['jml_soal']; $i++) {
			$_tjawab 	= "opsi_" . $i;
			$_tidsoal 	= "id_soal_" . $i;
			$_ragu 		= "rg_" . $i;
			$jawaban_ 	= empty($input[$_tjawab]) ? "" : $input[$_tjawab];
			$list_jawaban	.= "" . $input[$_tidsoal] . ":" . $jawaban_ . ":" . $input[$_ragu] . ",";
		}
		$list_jawaban	= substr($list_jawaban, 0, -1);
		$d_simpan = [
			'list_jawaban' => $list_jawaban
		];

		// Simpan jawaban
		$this->tugas->update('hasil_tugas', $d_simpan, 'id', $id_tes);
		$this->output_json(['status' => true]);
	}

	public function simpan_akhir()
	{
		// Decrypt Id
		$id_tes = $this->input->post('id', true);
		$id_tes = $this->encryption->decrypt($id_tes);
		$jenis_soal = $this->input->post('jenis', true);

		if ($jenis_soal == 'pilgan') {
			// Get Jawaban
			$list_jawaban = $this->tugas->getJawaban($id_tes);

			// Pecah Jawaban
			$pc_jawaban = explode(",", $list_jawaban);

			$jumlah_benar 	= 0;
			$jumlah_salah 	= 0;
			$jumlah_ragu  	= 0;
			$nilai_bobot 	= 0;
			$total_bobot	= 0;
			$jumlah_soal	= sizeof($pc_jawaban);

			foreach ($pc_jawaban as $jwb) {
				$pc_dt 		= explode(":", $jwb);
				$id_soal 	= $pc_dt[0];
				$jawaban 	= $pc_dt[1];
				$ragu 		= $pc_dt[2];

				$cek_jwb 	= $this->soal->getSoalById($id_soal);
				$total_bobot = $total_bobot + $cek_jwb->bobot;

				$jawaban == $cek_jwb->jawaban ? $jumlah_benar++ : $jumlah_salah++;
			}

			$nilai = ($jumlah_benar / $jumlah_soal)  * 100;
			$nilai_bobot = ($total_bobot / $jumlah_soal)  * 100;

			$d_update = [
				'jml_benar'		=> $jumlah_benar,
				'nilai'			=> number_format(floor($nilai), 0),
				'nilai_bobot'	=> number_format(floor($nilai_bobot), 0),
				'status'		=> 'N'
			];
			$this->tugas->update('hasil_tugas', $d_update, 'id', $id_tes);
			$this->output_json(['status' => TRUE, 'data' => $d_update, 'id' => $id_tes]);
		} else {
			$jawab = $this->input->post('jawaban', true);

			$d_update = [
				'list_jawaban' 	=> $jawab,
				'status'		=> 'N'
			];

			$this->tugas->update('hasil_tugas', $d_update, 'id', $id_tes);
			$this->output_json(['status' => TRUE, 'data' => $d_update, 'id' => $id_tes]);
		}
	}

	//===============================================================
	//-----------------------PENGUJIAN UNIT 2------------------------
	//===============================================================

	public function save_kuis_test($m = 24, $t = 13, $n = null, $j = null)
	{
		$this->load->helper('string');
		$form = array(
			'mapel_id' => $m,
			'topik' => $t,
			'jenis_soal' => $j,
			'guru_id' => 11,
			'nama_tugas' => $n,
			'jumlah_soal' => 1,
			'tgl_mulai' => '2020-01-05 12:14:47',
			'tgl_selesai' => '2020-01-06 12:14:47',
			'waktu' => 5,
			'jenis_tugas' => 'tugas',
			'jenis' => 'acak',
			'id_soal' => 4,
			'token' => strtoupper(random_string('alpha', 5))
		);
		$this->form_validation->set_data($form);
		if ($form['jenis_soal'] == 'pilgan') {
			$this->validasi($form['mapel_id'], $form['topik']);
			$this->form_validation->set_rules('waktu', 'Waktu', 'required|integer|max_length[4]|greater_than[0]');
		} else {
			$this->validasi_essay();
			$this->form_validation->set_rules('waktu', 'Waktu', 'required|integer|max_length[4]|greater_than[0]');
		}
		$mapel_id 		= $form['mapel_id'];
		$topik_id	 	= $form['topik'];
		$guru_id 		= $form['guru_id'];
		$nama_tugas 	= $form['nama_tugas'];
		$jumlah_soal 	= $form['jumlah_soal'];
		$tgl_mulai 		= $form['tgl_mulai'];
		$tgl_selesai	= $form['tgl_selesai'];
		$waktu			= $form['waktu'];
		$jenis_tugas	= $form['jenis_tugas'];
		$jenis_soal		= $form['jenis_soal'];
		$jenis			= $form['jenis'];
		$id_soal		= $form['id_soal'];
		$token 			= $form['token'];

		if ($this->form_validation->run() === FALSE) {
			$data['status'] = false;
			$data['errors'] = [
				'nama_tugas' 	=> form_error('nama_tugas'),
				'topik'		 	=> form_error('topik'),
				'jumlah_soal' 	=> form_error('jumlah_soal'),
				'tgl_mulai' 	=> form_error('tgl_mulai'),
				'tgl_selesai' 	=> form_error('tgl_selesai'),
				'waktu' 		=> form_error('waktu'),
				'jenis_soal'	=> form_error('jenis_soal'),
				'jenis' 		=> form_error('jenis')
			];
		} else {
			if ($jenis_soal == 'pilgan') {
				$input = [
					'nama_tugas' 	=> $nama_tugas,
					'topik_id'	 	=> $topik_id,
					'tgl_mulai' 	=> $tgl_mulai,
					'terlambat' 	=> $tgl_selesai,
					'waktu' 		=> $waktu,
					'jumlah_soal' 	=> $jumlah_soal,
					'jenis_tugas'	=> $jenis_tugas,
					'jenis_soal'	=> $jenis_soal,
					'jenis' 		=> $jenis,
				];
			} else {
				$input = [
					'nama_tugas' 	=> $nama_tugas,
					'topik_id'	 	=> $topik_id,
					'tgl_mulai' 	=> $tgl_mulai,
					'terlambat' 	=> $tgl_selesai,
					'waktu' 		=> $waktu,
					'jumlah_soal' 	=> 1,
					'jenis_tugas'	=> $jenis_tugas,
					'jenis_soal'	=> $jenis_soal,
					'id_soal_essay'	=> $id_soal,
				];
			}
			$input['guru_id']	= $guru_id;
			$input['mapel_id'] 	= $mapel_id;
			$input['token']		= $token;
			$action = $this->tugas->create('tugas', $input);

			$data['status'] = $action ? TRUE : FALSE;
		}
		return $data;
	}

	public function unit_test_B_1() //TEST CASE 1 : JENIS SOAL PILGAN, TAPI JUDUL TIDAK DIISI (GAGAL)
	{
		$test = $this->save_kuis_test(24, 13, null, "pilgan");  //isi parameter 1 dengan mapel id, parameter 2 dengan topik id, parameter 3 dengan judul tp dikosongi, parmeter 4 "pilgan"
		// print_r($test);
		$data['status'] = FALSE;
		$data['errors'] = [
			'nama_tugas' 	=> "The Nama Tugas/Kuis field is required.",
			'topik'		 	=> "",
			'jumlah_soal' 	=> "",
			'tgl_mulai' 	=> "",
			'tgl_selesai' 	=> "",
			'waktu' 		=> "",
			'jenis_soal'	=> "",
			'jenis' 		=> ""
		];
		$expected_result = $data;
		$test_name = 'TAMBAH TUGAS GAGAL';
		echo $this->unit->run($test, $expected_result, $test_name);
	}



	public function unit_test_B_2()  //TEST CASE 1 : JENIS SOAL ESSAY, TAPI JUDUL TIDAK DIISI (GAGAL)
	{
		$test = $this->save_kuis_test(24, 13, null, "essay"); //isi parameter 1 dengan mapel id, parameter 2 dengan topik id, parameter 3 dengan judul tp dikosongi, parmeter 4 "essay"
		// print_r($test);
		$data['status'] = FALSE;
		$data['errors'] = [
			'nama_tugas' 	=> "The Nama Tugas/Kuis field is required.",
			'topik'		 	=> "",
			'jumlah_soal' 	=> "",
			'tgl_mulai' 	=> "",
			'tgl_selesai' 	=> "",
			'waktu' 		=> "",
			'jenis_soal'	=> "",
			'jenis' 		=> ""
		];
		$expected_result = $data;
		$test_name = 'TAMBAH TUGAS GAGAL';
		echo $this->unit->run($test, $expected_result, $test_name);
	}

	public function unit_test_B_3()  //TEST CASE 1 : JENIS SOAL PILGAN (BERHASIL)
	{
		$test = $this->save_kuis_test(24, 13, "KD 1 Pilgan", "pilgan"); //isi parameter 1 dengan mapel id, parameter 2 dengan topik id, parameter 3 dengan judul "KD 1 Pilgan", parmeter 4 "pilgan"
		// print_r($test);
		$data['status'] = TRUE;

		$expected_result = $data;
		$test_name = 'TAMBAH TUGAS BERHASIL';
		echo $this->unit->run($test, $expected_result, $test_name);
	}

	public function unit_test_B_4() //TEST CASE 1 : JENIS SOAL ESSAY (BERHASIL)
	{
		$test = $this->save_kuis_test(24, 13, "KD 1 Essay", "essay"); //isi parameter 1 dengan mapel id, parameter 2 dengan topik id, parameter 3 dengan judul "KD 1 Essay", parmeter 4 "essay"
		// print_r($test);
		$data['status'] = TRUE;

		$expected_result = $data;
		$test_name = 'TAMBAH TUGAS BERHASIL';
		echo $this->unit->run($test, $expected_result, $test_name);
	}

	/** DISCLAIMER : DI CLASS INI ADA 1 PENGUJIAN UNIT SAJA
	 *  
	 * CARA MELAKUKAN PENGUJIAN UNIT:
	 * 
	 * 1. ISI PARAMETER SESUAI PETUNJUK
	 * 2. JALANKAN DI BROWSER DENGAN ALAMAT http://localhost/epembelajaranV2/{nama controller}/{unit test yg mau diuji}
	 * 	 contoh : http://localhost/epembelajaranV2/tugas/unit_test_B_1
	 * 3. variabel $test menampung hasil pengujian
	 *  	 variabel $expected_result menampung hasil yang diharapkan
	 *  	 variabel $test_name menampung nama pengujian (bisa diisi bebas)
	 * 	 $this->unit->run() berfungsi untuk menjalankan library unit_test pada CI
	 */
}
