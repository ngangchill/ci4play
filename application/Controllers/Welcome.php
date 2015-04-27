<?php namespace App\Controllers;

use CodeIgniter\Controller;

class Welcome extends Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	public function index($num=null)
	{
        var_dump($num);

        var_dump( config_item('migration.enabled') );

        echo "<h2>Home Controller</h2>";

        log_message('emergency', 'We {swear} made it.', ['swear' => 'fracking']);
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/Welcome.php */