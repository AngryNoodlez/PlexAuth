<?php

        function call_endpoint($url, $method = 'GET', $args = false)
        {
                $postdata = ($args) ? json_encode($args) : '';
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: '.strlen($postdata)));
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($ch);
                curl_close($ch);
                return ($response);
        }

        function update_plex_user($login = '', $libs = [], $deletemode = false)
        {

                // Configuration
                $token = $GLOBALS['ini_array']['token'];
                $server_id = $GLOBALS['ini_array']['server_id'];

                // Valid token
                if ($token === 'YOUR ADMIN PLEX TOKEN')
                        return (array('ERROR_TOKEN'));

                // Valid Server id
                if ($server_id === 'YOUR SERVER ID')
                        return (array('ERROR_SERVER_ID'));

                // Login parsing
                $login = trim(strtolower($login));
                if (empty($login))
                        return (array('ERROR_LOGIN'));

                // Get user list
                $list_xml = trim(strtolower(@file_get_contents('https://plex.tv/api/users?X-Plex-Token='.$token)));
                if (empty($list_xml))
                        return (array('ERROR_LIST'));

                // Extract Server Link ID
                $link_id = false;
                if (strpos($list_xml, $login) !== false)
                {
                        $link_id = explode('username="'.$login.'"', $list_xml)[1];
                        $link_id = explode('</user>', $link_id)[0];
                        if (strpos($link_id, '<server') !== false)
                        {
                                $link_id = explode('" serverid="', $link_id)[0];
                                $link_id = explode('<server id="', $link_id)[1];
                        }
                        else
                                $link_id = false;
                }

                // Extract User ID
                $user_id = false;
                if (strpos($list_xml, $login) !== false)
                {
                        $user_id = explode('username="'.$login.'"', $list_xml)[0];
                        $user_id = explode('<user id="', $user_id);
                        if (isset($user_id[count($user_id) - 1]))
                        {
                                $user_id = $user_id[count($user_id) - 1];                                                        
								$user_id = explode('"', $user_id)[0];
                        }
                        else
                                $user_id = false;
                }

                if (count($libs) === 0)
                {
                        if ($deletemode == true) {
                                // Delete mode
                                if ($link_id != false)
                                {
                                        $http_method = 'DELETE';
                                        $http_link = 'https://plex.tv/api/servers/'.$server_id.'/shared_servers/'.$link_id.'?X-Plex-Token='.$token;
                                        $http_body = false;
                                        $http_return = 'SUCCESS_DELETE';
                                }
                                else
                                        return (array('ERROR_DELETE_NO_SERVER'));
                        } else {
                                // Get share list
                                $share_xml = false;
                                $share_xml = file_get_contents('https://plex.tv/api/servers/'.$server_id.'/shared_servers/'.$link_id.'?X-Plex-Token='.$token);

                                // Extract Share IDs if user has any shares
                                if ($link_id)
                                {
                                        if ($share_xml !== false)
                                        {

                                                $sections = explode('<Section id=', $share_xml);

                                                $section_ids = array();
                                                $section_ids_all = array();

                                                foreach ($sections as $section) {
                                                        preg_match('/\"(\d+)\"\s.*title\=\"(.*)\"\stype\=\"(\w+)\"\sshared\=\"(\d)\".*/', $section, $section_ids);
                                                        if($section_ids[1] != '') {
                                                                $section_ids_all[] = array(
                                                                        'section' => $section_ids[1],
                                                                        'title' => $section_ids[2],
                                                                        'type' => $section_ids[3],
                                                                        'shared' => $section_ids[4],
                                                                );
                                                        }
                                                }

                                                return $section_ids_all;


                                        } else {
                                                return (array('ERROR_NO_SERVER'));;
                                        }
                                } else {
                                    if ($login !== "admin") {
                                    // If user has no shares, check if they're an admin. If not, present full list of shares
                                    $share_list_xml  = false;    
                                    $share_list_xml = file_get_contents('https://plex.tv/api/servers/'.$server_id.'/?X-Plex-Token='.$token);
                                    $sections = explode('<Section id=', $share_list_xml);
                            
                                    $section_ids = array();
                                    $section_ids_all = array();

                                    foreach ($sections as $section) {
                                        preg_match('/\"(\d+)\"\s.*type\=\"(\w+)\"\stitle\=\"(.*)\".*/', $section, $section_ids);
                                        if($section_ids[1] != '') {
                                            $section_ids_all[] = array(
                                            'section' => $section_ids[1],
                                            'title' => $section_ids[3],
                                            'type' => $section_ids[2],
                                            'shared' => '0',
                                            );
                                        }
                                    }
                                        return $section_ids_all;
                                    } else {
                                        return null;
                                    }
                                }
                }

                // Edit / Add mode
                else
                {
                        // Server update
						if ($link_id)
                        {
                                $http_method = 'PUT';
                                $http_link = 'https://plex.tv/api/servers/'.$server_id.'/shared_servers/'.$link_id.'?X-Plex-Token='.$token;
                                $http_body = array(
                                        "server_id" => $server_id,
                                        "shared_server" => array(
                                                "library_section_ids" => $libs
                                        )
                                );
                                $http_return = 'SUCCESS_UPDATE_LINK_ID';
                        }
                        // User update
                        elseif ($user_id)
                        {
                                $http_method = 'POST';
                                $http_link = 'https://plex.tv/api/servers/'.$server_id.'/shared_servers?X-Plex-Token='.$token;
                                $http_body = array(
                                        "server_id" => $server_id,
                                        "shared_server" => array(
                                                "library_section_ids" => $libs,
                                                "invited_id" => $user_id
                                        )
                                );
                                $http_return = 'SUCCESS_UPDATE_USER_ID';
                        }
                        // User create
                        else
                        {
                                $http_method = 'POST';
                                $http_link = 'https://plex.tv/api/servers/'.$server_id.'/shared_servers?X-Plex-Token='.$token;
                                $http_body = array(
                                        "server_id" => $server_id,
                                        "shared_server" => array(
                                                "library_section_ids" => $libs,
                                                "invited_email" => $login,
                                                "sharing_settings" => json_decode('{}')
                                        )
                                );
                                $http_return = 'SUCCESS_CREATE_USER';
                        }
                }

                // Execute request
                if (isset($http_method) && isset($http_link))
                {
                        $cb = call_endpoint($http_link, $http_method, $http_body);
                        return (array($http_return, $cb));
                }

                // Unknown error
                return (array('UNKNOWN_ERROR'));
        }

        // Get current user shares in JSON format
        // update_plex_user('Toto', [], false);
        //
        // Set libs to a specific user:
        // update_plex_user('Toto', [123, 256, 289], false);
        //
        // Delete libs to a specific user:
        // update_plex_user('Toto', [], true);
        //
