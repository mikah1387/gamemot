
document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('form input[type="text"]');

    inputs.forEach((input, index) => {
        input.addEventListener('input', function() {
            if (input.value.length === input.maxLength) {
                const nextInput = inputs[index + 1];
                if (nextInput) {
                    nextInput.focus();
                }
            }
        });
    });
});



console.log('hello');