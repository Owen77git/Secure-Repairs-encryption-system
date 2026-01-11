<?php
if (isset($_GET['download']) && $_GET['download'] == "word") {
  header("Content-type: application/vnd.ms-word");
  header("Content-Disposition: attachment;Filename=SecureRepair_Legal_Framework.doc");

  echo "
  <html xmlns:o='urn:schemas-microsoft-com:office:office'
        xmlns:w='urn:schemas-microsoft-com:office:word'
        xmlns='http://www.w3.org/TR/REC-html40'>
  <head><meta charset='utf-8'><title>SecureRepair Legal Framework</title></head>
  <body style='font-family:Segoe UI, sans-serif; color:#000;'>
    <div style='text-align:center;'>
      <h1 style='color:#ff0033;'>SecureRepair Electronics Shop</h1>
      <h2>Repair Shop Transparency & Legal Framework</h2>
      <hr style='border:2px solid #ff0033;'>
    </div>

    <p style='font-size:14pt; text-align:justify;'>
    At <b>SecureRepair Electronics Shop</b>, we firmly pledge to operate in full compliance with all applicable laws, regulations, and professional standards governing the repair and maintenance of electronic devices.<br>
    Our business is built on a foundation of trust, accountability, and respect for both our clients and the law.<br>
    We recognize that our operations have a direct impact on consumer safety, environmental protection, and data security; therefore, we promise to strictly follow all legal requirements related to consumer rights, warranty obligations, environmental disposal of electronic waste, and occupational safety.<br>
    Every service provided by SecureRepair will be conducted transparently, ensuring customers receive honest assessments, genuine replacement parts, and fair pricing at all times.<br><br>

    All <b>technicians</b> working under SecureRepair Electronics Shop also make a personal and professional commitment to uphold these standards with the utmost integrity.<br>
    They agree to follow all industry best practices, safety procedures, and company policies designed to promote lawful conduct and quality service.<br>
    Each technician understands the legal and ethical responsibility involved in handling customer devices, especially those containing sensitive or personal information, and promises to maintain strict confidentiality at all times.<br>
    SecureRepair further ensures that all technicians are properly trained and certified to meet technical and legal standards in their respective areas of expertise.<br><br>

    Together, the management and technicians of SecureRepair Electronics Shop vow to maintain complete transparency in every transaction, avoid any form of deception or malpractice, and cooperate fully with regulatory bodies whenever required.<br>
    We are committed to continuous improvement, ensuring our practices evolve alongside emerging laws and technologies.<br>
    By honoring this promise, SecureRepair Electronics Shop aims to strengthen customer confidence, uphold the reputation of the repair industry, and serve as a trusted, law-abiding service provider dedicated to professionalism, quality, and integrity.
    </p>

    <p><b>Researcher:</b> Faith Githinji (DCF-01-0110/2024)</p>
    <br><br>
    <p><b>Signature:</b> ________________________________</p>
    <p><b>Date:</b> _________________________________</p>

    <div style='text-align:center; margin-top:30px; font-size:12pt; color:#ff0033;'>
      © 2025 SecureRepair Systems | Data Integrity & Client Transparency
    </div>
  </body></html>";
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Legal Framework | SecureRepair</title>
  <style>
    body {
      background: #fff;
      color: #000;
      font-family: 'Segoe UI', sans-serif;
      max-width: 800px;
      margin: 50px auto;
      padding: 40px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    header {
      text-align: center;
      border-bottom: 3px solid #ff0033;
      margin-bottom: 25px;
      padding-bottom: 10px;
    }
    header h1 {
      color: #ff0033;
      margin-bottom: 5px;
    }

    .signature {
      margin-top: 40px;
      border-top: 2px solid #ff0033;
      padding-top: 20px;
      font-size: 14pt;
    }
    .signature-name {
      font-family: 'Brush Script MT', cursive;
      font-size: 20pt;
      color: #000;
    }
    section {
      margin-bottom: 30px;
      line-height: 1.6;
    }
    h2 {
      color: #ff0033;
      border-left: 4px solid #ff0033;
      padding-left: 10px;
      margin-bottom: 10px;
    }
    .signature {
      margin-top: 40px;
      border-top: 2px solid #ff0033;
      padding-top: 20px;
    }
    footer {
      text-align: center;
      margin-top: 40px;
      border-top: 1px solid #ccc;
      font-size: 13px;
      color: #555;
      padding-top: 10px;
    }
    .download-btn {
      background: #ff0033;
      color: #fff;
      border: none;
      padding: 10px 20px;
      font-size: 15px;
      border-radius: 6px;
      cursor: pointer;
      display: block;
      margin: 30px auto 0;
      transition: 0.3s;
      text-decoration: none;
      text-align: center;
    }
    .download-btn:hover {
      background: #fff;
      color: #ff0033;
      border: 2px solid #ff0033;
      box-shadow: 0 0 10px rgba(255,0,51,0.3);
    }
  </style>
</head>
<body>

  <header>
    <h1>SecureRepair Electronics Shop</h1>
    <p><em>Repair Shop Transparency & Legal Framework</em></p>
  </header>

  <section>
    <h2>Legal Declaration</h2>
    <p>
      At <b>SecureRepair Electronics Shop</b>, we firmly pledge to operate in full compliance with all applicable laws, regulations, and professional standards governing the repair and maintenance of electronic devices.<br>
      Our business is built on a foundation of trust, accountability, and respect for both our clients and the law.<br>
      We recognize that our operations have a direct impact on consumer safety, environmental protection, and data security; therefore, we promise to strictly follow all legal requirements related to consumer rights, warranty obligations, environmental disposal of electronic waste, and occupational safety.<br>
      Every service provided by SecureRepair will be conducted transparently, ensuring customers receive honest assessments, genuine replacement parts, and fair pricing at all times.<br><br>

      All <b>technicians</b> working under SecureRepair Electronics Shop also make a personal and professional commitment to uphold these standards with the utmost integrity.<br>
      They agree to follow all industry best practices, safety procedures, and company policies designed to promote lawful conduct and quality service.<br>
      Each technician understands the legal and ethical responsibility involved in handling customer devices, especially those containing sensitive or personal information, and promises to maintain strict confidentiality at all times.<br>
      SecureRepair further ensures that all technicians are properly trained and certified to meet technical and legal standards in their respective areas of expertise.<br><br>

      Together, the management and technicians of SecureRepair Electronics Shop vow to maintain complete transparency in every transaction, avoid any form of deception or malpractice, and cooperate fully with regulatory bodies whenever required.<br>
      We are committed to continuous improvement, ensuring our practices evolve alongside emerging laws and technologies.<br>
      By honoring this promise, SecureRepair Electronics Shop aims to strengthen customer confidence, uphold the reputation of the repair industry, and serve as a trusted, law-abiding service provider dedicated to professionalism, quality, and integrity.
    </p>

    <p><strong>Shop Manager:</strong> Faith Githinji</p>
  </section>

 <section class="signature">
    <p><strong>Signature:</strong> <span class="signature-name">Faith Githinji</span></p>
    <p><strong>Date:</strong> <?php
      $date = new DateTime();
      $date->modify('+1 month');
      $date->modify('next monday');
      echo $date->format('l, d F Y');
    ?></p>
  </section>

  <a href='?download=word' class='download-btn'>Download Legal Document (DOC)</a>

  <footer>
    © 2025 SecureRepair Systems | Data Integrity & Client Transparency
  </footer>

</body>
</html>
