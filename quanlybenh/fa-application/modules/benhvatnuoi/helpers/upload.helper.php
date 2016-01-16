<?php
if (!function_exists('upload_file'))
{
    /**
     * @param string $dest_dir
     * @param array $file_data
     * @param string $new_base_file_name
     * @param bool|true $overwritten
     * @return bool|string
     */
    function upload_file($dest_dir, $file_data, $new_base_file_name, $overwritten = true)
    {
        if (!is_array($file_data))
        {
            //error('Dữ liệu file không hợp lệ', 'function_upload_file');
            return false;
        }
        if (empty($file_data['name']))
        {
            //error('Không tìm thấy file name', 'function_upload_file');
            return false;
        }
        $allow_file_type = array(
            'image/jpeg',
            'image/png',
            'image/gif'
        );
        $allow_file_ext = array(
            'jpg',
            'png',
            'gif'
        );
        if (!in_array($file_data['type'], $allow_file_type))
        {
            //error('Không cho phép upload định dạng file này', 'function_upload_file');
            return false;
        }

        $file_ext = pathinfo($file_data['name'], PATHINFO_EXTENSION);
        if (!in_array($file_ext, $allow_file_ext))
        {
            //error('Không cho phép file với đuôi .' . $file_ext, 'function_upload_file');
            return false;
        }

        $dest_dir = rtrim($dest_dir, '/');
        $tmp_name = $file_data['tmp_name'];
        if (!$new_base_file_name)
        {
            $new_base_file_name = pathinfo($file_data['name'], PATHINFO_FILENAME);
        }
        else
        {
            $new_base_file_name = pathinfo($new_base_file_name, PATHINFO_FILENAME);
        }

        $new_file_name = $new_base_file_name . '.' . $file_ext;
        $new_file = $dest_dir . '/' . $new_file_name;

        if (!$overwritten)
        {
            if (file_exists($new_file))
            {
                $a = time() - rand(100000, 999999);
                $new_base_file_name = $new_base_file_name . '-' . $a;
                $new_file_name = $new_base_file_name . '.' . $file_ext;
                $new_file = $dest_dir . '/' . $new_file_name;
            }
        }
        if (move_uploaded_file($tmp_name, $new_file))
        {
            return $new_file_name;
        }
        else
        {
            //error('Lỗi trong khi upload file', 'function_upload_file');
            return false;
        }
    }

}
