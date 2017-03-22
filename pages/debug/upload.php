<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>upload</title>
    <link href="/assets/debug/dropzone/dropzone.min.css" rel="stylesheet">
    <style>
        .box {
            border: 1px solid #5d5d5d;
            width: 100%;
            margin: 0 auto;
        }
    </style>
</head>
<body>
文件上传：
<div id="dropzone" class="dropzone"></div>
<br>
返回值：
<div id="res" class="box"></div>
代码：
<pre>
<textarea rows=15 cols=150>
    <div id="dropzone" class="dropzone"></div>
    <script type="text/javascript">
        Dropzone.autoDiscover = false;
        $('#dropzone').dropzone({
            url: "/api/upload/attach",
            withCredentials: true,
            init: function () {
                this.on("success", function (file, res) {
                    console.log(file);
                    document.querySelector('#res').innerHTML = JSON.stringify(res);
                });
            }
        });
    </script>
</textarea>
</pre>

DEMO2:
<input id="upload" type="file" name="file" accept="image/jpg,image/jpeg,image/png,image/gif" class="weui-uploader__input" multiple="" style="">
<div id="demo2" class="box"></div>
<script src="//static.cifuwu.com/jquery/3.1.1/jquery.min.js" type="text/javascript"></script>
<script src="/assets/debug/dropzone/dropzone.min.js" type="text/javascript"></script>
<script type="text/javascript">
    jQuery.support.cors = true;
    Dropzone.autoDiscover = false;
    $('#dropzone').dropzone({
        url: "/api/upload/attach",
        withCredentials: true,
        init: function () {
            this.on("success", function (file, res) {
                console.log(file);
                document.querySelector('#res').innerHTML = JSON.stringify(res);
            });
        }
    });
</script>

<script type="text/javascript">
    $(document).on("change", "#upload", Upload);
    function Upload() {
        var upload = new ZJSDTY.Upload();
        var file = this.files[0];
        this.value = null;
        upload.Image(file, function (callback) {
            document.querySelector('#demo2').innerHTML = JSON.stringify(callback);
        });
    }
    var ZJSDTY = {};
    ZJSDTY.Upload = function () {
        var _this = this;

        this.acceptUrl = "/upload/image/stream";
        this.filePath = "/storage/image/";

        this.onCompleted = function () {
            console.log("undefined callback function onCompleted");
        };
        this.onFailed = function () {
            console.log("undefined callback function onFailed");
        };
        var stream;
        var resize = 0;

        this.Image = function (file, completed, failed) {

            if (typeof (file) == "undefined") {
                return;
            }
            if (!/image\/\w+/.test(file.type)) {
                console.log("Image Format Error");
                return;
            }
            if (typeof (completed) == "function") {
                _this.onCompleted = completed;
            }
            if (typeof (failed) == "function") {
                _this.onFailed = failed;
            }
            var reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = function () {
                stream = this.result;
                if (file.size <= 1024000) {
                    resize = 0;
                } else {
                    resize = 1;
                }
                UploadAsStream();
            };
        };

        this.Stream = function (base64, completed, failed) {
            stream = base64;
            if (typeof (completed) == "function") {
                _this.onCompleted = completed;
            }
            if (typeof (failed) == "function") {
                _this.onFailed = failed;
            }
            UploadAsStream();
        };

        var UploadAsStream = function () {
            $.ajax({
                async: true,
                url: _this.acceptUrl,
                data: {
                    file: stream,
                    resize: resize
                },
                type: 'post',
                dataType: 'json',
                success: function (callback) {
                    if (callback.code == 20000) {
                        _this.onCompleted(callback);

                    } else {
                        _this.onFailed(callback);
                    }
                }
            });

        };

    };
</script>

</body>
</html>