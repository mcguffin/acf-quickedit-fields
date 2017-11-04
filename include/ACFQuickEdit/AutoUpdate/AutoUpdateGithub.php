<?php

namespace ACFQuickEdit\AutoUpdate;

class AutoUpdateGithub extends AutoUpdate {

	private $github_repo = null;

	/**
	 *	@inheritdoc
	 */
	public function get_release_info() {
		if ( $release_info_url = $this->get_release_info_url() ) {
			$response = wp_remote_get( $release_info_url, array() );
			if ( ! is_wp_error( $response ) ) {
				$release_info = json_decode( wp_remote_retrieve_body( $response ) );
				$id = sprintf( 'github.com/%s', $this->get_github_repo() );
				$version = preg_replace( '/^([^0-9]+)/ims', '', $release_info->tag_name );
				return array(
					'id'			=> $id,
					'version'		=> $version,
					'download_url'	=> $release_info->zipball_url
				);
			}
		}
		return false;
	}


	/**
	 *	@return	string	github-owner/github-repo
	 */
	private function get_github_repo() {
		if ( is_null( $this->github_repo ) ) {
			$this->github_repo = false;
			$data = get_file_data( GITUPDATE_TEST_FILE, array('GithubRepo'=>'Github Repository') );
			if ( ! empty( $data['GithubRepo'] ) ) {
				$this->github_repo = $data['GithubRepo'];
			}
		}
		return $this->github_repo;

	}

	/**
	 *	@return	string	github api url
	 */
	private function get_release_info_url() {
		$url = false;
		if ( $repo = $this->get_github_repo() ) {
			$url = sprintf('https://api.github.com/repos/%s/releases/latest', $repo );
		}
		return $url;
	}

}
