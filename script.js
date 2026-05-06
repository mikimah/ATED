const form = document.getElementById("registrationForm");
const firstname = document.getElementById("firstname");
const lastname = document.getElementById("lastname");
const email = document.getElementById("mail");
const message = document.getElementById("message");
const congressName = document.getElementById("congress-name");
 const goToTopBtn = document.getElementById('goToTopBtn');



function scrollToSection(id) {
  const element = document.getElementById(id);
    if (element) {
      element.scrollIntoView({ behavior: 'smooth' });
      window.history.replaceState(null, null, window.location.pathname);
    }
}

      // Show/Hide Go to Top button
     
window.addEventListener('scroll', function() {
  if (window.scrollY > 300) {
    goToTopBtn.style.display = 'flex';
    } else {
    goToTopBtn.style.display = 'none';
    }
});

if (performance.getEntriesByType("navigation")[0].type === "reload") {
  if (window.scrollY > 300) {
    goToTopBtn.style.display = 'flex';
    } else {
    goToTopBtn.style.display = 'none';
    }
}

form.addEventListener("submit", async (e) => {
  e.preventDefault();

  const emailValue = email.value.trim();
  const firstnameValue = firstname.value.trim();
  const lastnameValue = lastname.value.trim();
  const congressNameValue = congressName.value.trim();
  const messageValue = message.value.trim();


  if (messageValue.split(" ").length > 300) {
    alert("Message must not exceed 300 characters.");
    return;
  }

  const formData = new FormData();
  formData.append("email", emailValue);
  formData.append("firstname", firstnameValue);
  formData.append("lastname", lastnameValue);
  formData.append("congress-name", congressNameValue);
  formData.append("message", messageValue);

  const response = await fetch("register.php", {
    method: "POST",
    body: formData
  });

  const result = await response.json();

  if (result.status === "duplicate") {
    alert("Mail is duplicated");
  } else if (result.status === "success") {
    alert("Registration saved successfully.");
    form.reset();
  } else {
    alert("Something went wrong.");
  }
});