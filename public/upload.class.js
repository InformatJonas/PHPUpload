/**
 * Upload File Helper for Styling
 * @author Felix Schürmeyer
 */

console.log('Start Upload File Helper');

document.addEventListener(
    "DOMContentLoaded",
    function (event) {
        let input = document.querySelectorAll("input[type=file]");
        for (let i = 0; i < input.length; i++) {
            let inputFile = input[i];
            inputFile.addEventListener(
                'change',
                function () {
                    let label = this.nextElementSibling;

                    if (this.files && this.files.length > 1) {
                        label.innerHTML = this.files.length + ' ' + ts['files']; // @todo: vorher kommt ts?
                    } else {
                        label.innerHTML = this.files[0].name + ' ' + ts['file']; // @todo: vorher kommt ts?
                    }
                }
            );
        }
    }
);