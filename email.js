async function submitForm(event) {
    event.preventDefault();

    const submitButton = document.getElementById("Submit"); // Get the submit button

    // Disable button to prevent multiple clicks
    submitButton.disabled = true;
    submitButton.value = "Sending..."; // Update button text

    // Get form values
    const name = document.getElementById('name').value;
    const email = document.getElementById('email').value;
    const message = document.getElementById('message').value;

    // Validate inputs
    if (!name || !email || !message) {
        alert('Please fill in all fields.');
        submitButton.disabled = false; // Re-enable button if validation fails
        submitButton.value = "Send"; // Restore button text
        return;
    }

    try {
        // Send form data to the backend
        const response = await fetch('http://localhost:5000/send-message', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ name, email, message }),
        });

        if (response.ok) {
            alert('Message sent successfully!');
            document.getElementById('contactform').reset(); // Reset form
        } else {
            alert('Failed to send message. Please try again.');
        }
    } catch (error) {
        console.error("Error sending message:", error);
        alert("An error occurred. Please try again.");
    } finally {
        // Re-enable button after request is complete
        submitButton.disabled = false;
        submitButton.value = "Send"; // Restore button text
    }
}
