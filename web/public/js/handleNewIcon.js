getSession.then(d => {
    let imgSpot = document.querySelector('.change-icon');
    let user_id = d.user.id;

    f.addEventListener('change', function (e) {
        console.log(3);
        let reader = new FileReader()
        let file = e.target.files[0];
        reader.readAsDataURL(file)
        reader.onloadend = () => {
            let imgBASE64 = reader.result;
            imgSpot.innerHTML += '<div class="choose-img"><img src='+imgBASE64+' id="image"><span id="acceptNewIcon">Применить</span></div>';
            var image = document.querySelector('#image');
            var minAspectRatio = 0.5;
            var maxAspectRatio = 1.5;
            var cropper = new Cropper(image, {
                ready: function () {
                var cropper = this.cropper;
                var containerData = cropper.getContainerData();
                var cropBoxData = cropper.getCropBoxData();
                var aspectRatio = cropBoxData.width / cropBoxData.height;
                var newCropBoxWidth;

                if (aspectRatio < minAspectRatio || aspectRatio > maxAspectRatio) {
                    newCropBoxWidth = cropBoxData.height * ((minAspectRatio + maxAspectRatio) / 2);

                    cropper.setCropBoxData({
                    left: (containerData.width - newCropBoxWidth) / 2,
                    width: newCropBoxWidth
                    });
                }
                },

                cropmove: function () {
                var cropper = this.cropper;
                var cropBoxData = cropper.getCropBoxData();
                var aspectRatio = cropBoxData.width / cropBoxData.height;

                if (aspectRatio < minAspectRatio) {
                    cropper.setCropBoxData({
                    width: cropBoxData.height * minAspectRatio
                    });
                } else if (aspectRatio > maxAspectRatio) {
                    cropper.setCropBoxData({
                    width: cropBoxData.height * maxAspectRatio
                    });
                }
                },
            });

            acceptNewIcon.addEventListener('click', ()=>uploadNewIcon(cropper));
        }
    });


    function uploadNewIcon(cropper) {
        let newIcon = cropper.getCroppedCanvas().toDataURL('image/jpeg');
        
        sendJSON('../ajax/apply_new_icon.php', {'data': newIcon, 'login': user_id})
        .then((data) => data.text())
        .then(data => {
            document.querySelector('.choose-img').remove();
            location.href = '/profile/'+user_id;
        })
    }
})