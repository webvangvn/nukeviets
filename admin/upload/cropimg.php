<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 2-2-2010 12:55
 */

if( ! defined( 'NV_IS_FILE_ADMIN' ) ) die( 'Stop!!!' );

$path = nv_check_path_upload( $nv_Request->get_string( 'path', 'post,get' ) );
$check_allow_upload_dir = nv_check_allow_upload_dir( $path );

if( ! isset( $check_allow_upload_dir['delete_file'] ) )
{
	die( 'ERROR#' . $lang_module['notlevel'] );
}

$file = htmlspecialchars( trim( $nv_Request->get_string( 'file', 'post,get' ) ), ENT_QUOTES );
$file = basename( $file );

if( empty( $file ) or ! is_file( NV_ROOTDIR . '/' . $path . '/' . $file ) )
{
	die( 'ERROR#' . $lang_module['errorNotSelectFile'] . NV_ROOTDIR . '/' . $path . '/' . $file );
}

if( $nv_Request->isset_request( 'path', 'post' ) and $nv_Request->isset_request( 'x', 'post' ) and $nv_Request->isset_request( 'y', 'post' ) )
{
	$config_logo = array();
	$config_logo['x'] = $nv_Request->get_int( 'x', 'post', 0 );
	$config_logo['y'] = $nv_Request->get_int( 'y', 'post', 0 );
	$config_logo['w'] = $nv_Request->get_int( 'w', 'post', 0 );
	$config_logo['h'] = $nv_Request->get_int( 'h', 'post', 0 );

	if( $config_logo['w'] > 0 and $config_logo['h'] > 0 )
	{
		require_once NV_ROOTDIR . '/includes/class/image.class.php';
		$createImage = new image( NV_ROOTDIR . '/' . $path . '/' . $file, NV_MAX_WIDTH, NV_MAX_HEIGHT );
        $createImage->cropFromLeft( $config_logo['x'], $config_logo['y'], $config_logo['w'], $config_logo['h'] );
		$createImage->save( NV_ROOTDIR . '/' . $path, $file );
		$createImage->close();

		if( isset( $array_dirname[$path] ) )
		{
			if( preg_match( '/^' . nv_preg_quote( NV_UPLOADS_DIR ) . '\/(([a-z0-9\-\_\/]+\/)*([a-z0-9\-\_\.]+)(\.(gif|jpg|jpeg|png)))$/i', $path . '/' . $file, $m ) )
			{
				@nv_deletefile( NV_ROOTDIR . '/' . NV_FILES_DIR . '/' . $m[1] );
			}

			$info = nv_getFileInfo( $path, $file );

			$did = $array_dirname[$path];
			$db->query( "UPDATE " . NV_UPLOAD_GLOBALTABLE . "_file SET filesize=" . $info['filesize'] . ", src='" . $info['src'] . "', srcwidth=" . $info['srcwidth'] . ", srcheight=" . $info['srcheight'] . ", sizes='" . $info['size'] . "', userid=" . $admin_info['userid'] . ", mtime=" . $info['mtime'] . " WHERE did = " . $did . " AND title = '" . $file . "'" );
		}

		die( 'OK#' . basename( $file ) );
	}
	else
	{
		die( 'ERROR#' . $lang_module['notlevel'] );
	}
}

die( 'ERROR#Error Access!!!' );