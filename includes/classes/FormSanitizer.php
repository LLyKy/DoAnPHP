<?php
class FormSanitizer{
    // Phương thức này được sử dụng để làm sạch các chuỗi văn bản nhập vào từ biểu mẫu
    public static  function sanitizeFormString($inputText){
        // Loại bỏ tất cả các thẻ HTML khỏi chuỗi văn bản.
        $inputText = strip_tags($inputText);
        // $inputText = str_replace(" ","",$inputText);
        //  Loại bỏ các khoảng trắng ở đầu và cuối chuỗi.
        $inputText = trim($inputText);
        // Chuyển đổi chuỗi về dạng chữ thường.
        $inputText = strtolower($inputText);
        // Chuyển đổi chữ cái đầu tiên của chuỗi thành chữ hoa.
        $inputText = ucfirst($inputText);
        return $inputText;
    }

    // Phương thức này được sử dụng để làm sạch tên người dùng nhập vào từ biểu mẫu.
    public static  function sanitizeFormUsername($inputText){
        //  Loại bỏ tất cả các thẻ HTML khỏi chuỗi.
        $inputText = strip_tags($inputText);
        // Loại bỏ tất cả các khoảng trắng trong chuỗi.
        $inputText = str_replace(" ","",$inputText);
        return $inputText;
    }
    //Phương thức này được sử dụng để làm sạch mật khẩu nhập vào từ biểu mẫu
    public static  function sanitizeFormPassword($inputText){
        // Loại bỏ tất cả các thẻ HTML khỏi chuỗi
        $inputText = strip_tags($inputText);
        return $inputText;
    }
     public static  function sanitizeFormEmail($inputText){
        // Loại bỏ tất cả các thẻ HTML khỏi chuỗi
        $inputText = strip_tags($inputText);
        // Loại bỏ tất cả các khoảng trắng trong chuỗi
        $inputText = str_replace(" ","",$inputText);
        return $inputText;
    }

}
