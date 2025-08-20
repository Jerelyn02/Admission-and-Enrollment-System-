<?php
    include('..\php\connection.php');
    session_start();
?>
<?php include('../php/useradmissionheader.php'); ?>

<!-- Link the new dashboard CSS -->
<link rel="stylesheet" href="../css/prodprog.css">


<div class="content">
    <div class="maincontainer">

        <!-- Admission Steps -->
        <div class="card">
            <h2>Freshmen Admission Requirements</h2>
            <div class="card-content">
                <ol>
                    <li>Fill up the application form and upload requirements.</li>
                    <li>Validation and Evaluation of documents.</li>
                    <li>Entrance Examination.</li>
                    <li>Issuance of Notice of Admission (NOA).</li>
                    <li>Secure medical referral slip and proceed to medical exam.</li>
                    <li>Claim medical clearance.</li>
                    <li>Submit to Requirements:
                        <ul>
                            <li>Grade 12 Report Card (or Certificate of Rating for ALS)</li>
                            <li>Good Moral Certificate</li>
                            <li>Notice of Admission (NOA)</li>
                            <li>Medical Clearance</li>
                        </ul>
                    </li>
                </ol>
            </div>
        </div>

        <div class="card">
            <h2>Transferee Admission Requirements</h2>
            <div class="card-content">
                <ol>
                    <li>Fill up the application form and upload requirements.</li>
                    <li>Validation and Evaluation of documents.</li>
                    <li>Issuance of Notice of Admission (NOA).</li>
                    <li>Secure medical referral slip and proceed to medical exam.</li>
                    <li>Claim medical clearance.</li>
                    <li>Submit to Requirements:
                        <ul>
                            <li>Transcript of Records</li>
                            <li>Good Moral Certificate</li>
                            <li>Honorable Dismissal</li>
                            <li>Notice of Admission (NOA)</li>
                            <li>Medical Clearance</li>
                            <li>NBI or Police Clearance</li>
                        </ul>
                    </li>
                </ol>
            </div>
        </div>

        <div class="card">
            <h2>Returnee / Re-admission Requirements</h2>
            <div class="card-content">
                <ol>
                    <li>Fill up the application form and upload requirements.</li>
                    <li>Validation and Evaluation of previous school records.</li>
                    <li>Issuance of Notice of Admission (NOA).</li>
                    <li>Secure medical referral slip and proceed to medical exam (if applicable).</li>
                    <li>Claim medical clearance (if applicable).</li>
                    <li>Submit Requirements:
                        <ul>
                            <li>Transcript of Records</li>
                            <li>Notice of Admission (NOA)</li>
                            <li>Medical Clearance (if applicable)</li>
                            <li>Good Moral Certificate (if applicable)</li>
                        </ul>
                    </li>
                </ol>
            </div>
        </div>

        <div class="card">
            <h2>Shiftee Admission Requirements</h2>
            <div class="card-content">
                <ol>
                    <li>Fill up the application form and upload requirements.</li>
                    <li>Validation and Evaluation of documents.</li>
                    <li>Issuance of Notice of Admission (NOA).</li>
                    <li>Secure medical referral slip and proceed to medical exam (if applicable).</li>
                    <li>Claim medical clearance (if applicable).</li>
                    <li>Submit Requirements:
                        <ul>
                            <li>Transcript of Records</li>
                            <li>Good Moral Certificate</li>
                            <li>Notice of Admission (NOA)</li>
                            <li>Medical Clearance (if applicable)</li>
                        </ul>
                    </li>
                </ol>
            </div>
        </div>

        <!-- Programs Section -->
<div class="card">
    <h2>Program Offerings</h2>
    <div class="card-content">
        <div class="program-grid">
            <div class="program-box">
                <strong>BSBM - Bachelor of Science in Business Management</strong>
                <p>Prepares students for leadership roles in business, finance, and entrepreneurship. Aligned Strand: ABM</p>
            </div>
            <div class="program-box">
                <strong>BSCS - Bachelor of Science in Computer Science</strong>
                <p>Focuses on algorithms, programming, and computing theory. Aligned Strand: STEM</p>
            </div>
            <div class="program-box">
                <strong>BSIT - Bachelor of Science in Information Technology</strong>
                <p>Emphasizes software development, networking, and IT systems management. Aligned Strand: ICT or STEM</p>
            </div>
            <div class="program-box">
                <strong>BSCE - Bachelor of Science in Civil Engineering</strong>
                <p>Covers structural design, construction, and infrastructure systems. Aligned Strand: STEM</p>
            </div>
            <div class="program-box">
                <strong>BSEE - Bachelor of Science in Electrical Engineering</strong>
                <p>Focuses on circuits, electronics, and electrical system design. Aligned Strand: STEM</p>
            </div>
            <div class="program-box">
                <strong>BSCEng - Bachelor of Science in Civil Engineering</strong>
                <p>Covers advanced civil engineering topics and construction management. Aligned Strand: STEM</p>
            </div>
            <div class="program-box">
                <strong>BSME - Bachelor of Science in Mechanical Engineering</strong>
                <p>Focuses on mechanics, thermodynamics, and machine design. Aligned Strand: STEM</p>
            </div>
            <div class="program-box">
                <strong>BSECE - Bachelor of Science in Electronics Engineering</strong>
                <p>Emphasizes electronic devices, systems, and communication technologies. Aligned Strand: STEM</p>
            </div>
            <div class="program-box">
                <strong>BSMA - Bachelor of Science in Management Accounting</strong>
                <p>Prepares students for accounting and finance careers. Aligned Strand: ABM</p>
            </div>
            <div class="program-box">
                <strong>BSE - Bachelor of Science in Entrepreneurship</strong>
                <p>Focuses on business creation, innovation, and small business management. Aligned Strand: ABM</p>
            </div>
            <div class="program-box">
                <strong>BSPA - Bachelor of Science in Public Administration</strong>
                <p>Prepares students for government and public sector management roles. Aligned Strand: HUMSS</p>
            </div>
            <div class="program-box">
                <strong>BSHM - Bachelor of Science in Hospitality Management</strong>
                <p>Covers hotel, restaurant, and tourism operations. Aligned Strand: HUMSS</p>
            </div>
            <div class="program-box">
                <strong>BSTM - Bachelor of Science in Tourism Management</strong>
                <p>Focuses on tourism industry management and travel services. Aligned Strand: HUMSS</p>
            </div>
            <div class="program-box">
                <strong>BSIE - Bachelor of Science in Industrial / Technology Education</strong>
                <p>Prepares students to teach technical and industrial subjects. Aligned Strand: TECH-VOC</p>
            </div>
            <div class="program-box">
                <strong>BEED - Bachelor of Elementary Education</strong>
                <p>Prepares students for teaching in elementary schools. Aligned Strand: HUMSS</p>
            </div>
            <div class="program-box">
                <strong>BSED - Bachelor of Secondary Education</strong>
                <p>Prepares students for teaching in secondary schools. Aligned Strand: HUMSS</p>
            </div>
        </div>
    </div>
</div>

<!-- Collapse logic -->
<script>
document.querySelectorAll('.card h2').forEach(header => {
    header.addEventListener('click', () => {
        const card = header.parentElement;
        card.classList.toggle('active');
    });
});
</script>

</body>
</html>
