<?php
/**
 * Seed missing about / director content settings
 */
require_once dirname(__DIR__) . '/config/config.php';

$seeds = [
    'about_overview' => 'Urji Beri School offers education for children aged 3 to 13. Its curriculum is based on the programs of the Ministry of Education and taught in Amharic and English language. Our school is accredited by the Oromia Education Bureau for Preschool and Elementary Education and strives to achieve rigorous standards, continuous improvement, and creative individual accomplishment.',
    'about_mission' => 'We support a safe, caring, respectful environment that values creativity, diversity, and inclusivity. Develop self-aware learners with the tools for fulfillment in their world and beyond. Provide best practice learning that empowers individuals to set and reach high standards. Encourage students to think globally and act locally.',
    'about_vision' => 'To empower students to acquire, demonstrate, and value knowledge and skills as lifelong learners who contribute to the global world.',
    'about_values' => 'We believe in learner agency and the power of inquiry. There is strength in diversity and inclusivity. That we all should listen thoughtfully to others and consider their points of view. We learn best when we feel safe, happy, valued, and challenged. It is important to strive to be the best you can be. We should look beyond ourselves and seek to make genuine, positive, sustainable changes in the world around us.',
    'about_benefits' => 'Our school is an outstanding school with a warm and welcoming learning environment. We place special emphasis on being supportive of the diverse needs of a varied and dynamic school community and on offering all students opportunities for growth and success. Our purpose-built school, nationally inspected and accredited, caring teachers, true Ethiopian learning experience, and individualized approach are designed to enable your child to succeed at school and beyond.',
    'about_accreditation' => 'Urji Beri School is fully accredited by the Oromia Education Bureau and follows the Ministry of Education curriculum, enhanced with modern teaching methodologies and global best practices.',
    'director_name' => 'Mr. Alemayehu Aga',
    'director_title' => 'General Manager',
    'director_quote' => '"The more that you read, the more things you will know. The more that you learn, the more places you\'ll go." – Dr. Seuss',
    'director_message' => "I would like to take this opportunity to welcome you to our website and thank you for considering Urji Beri School as an educational home for your children. Whether you are considering a move to Alemgena, have just newly arrived or call Alemgena home, UBS is here to help you. We are the best primary school in Ethiopia and we offer good quality facilities, best teaching and a student centred approach to learning. The challenges and opportunities of a small, caring, exciting and rigorous education await you. More than that, UBS is a diverse, exciting community where students and families from all over the world come together to share ideas, discuss perspectives and learn from each other.\n\nThe school has strong practices in place to support students as they transition to, through and beyond the school and specialist staff that support all the learning needs of its students. At UBS we are committed to our Vision of being a school that develops and empowers future innovators and leaders. We work to give our very best in all our endeavors, and we invite you to become close partners in this important task. I personally extend a very warm welcome to UBS – a great place to grow and learn!",
    'learner_inquirer' => 'Acquires skills for purposeful, constructive research.',
    'learner_thinker' => 'Applies thinking skills critically and creatively to solve complex problems.',
    'learner_communicator' => 'Receives & expresses ideas in more than one language including the language of mathematical symbols.',
    'learner_risk_taker' => 'Approaches unfamiliar situations with confidence.',
    'learner_principled' => 'Displays integrity, honesty and a sense of fairness and justice.',
    'learner_caring' => 'Develops a sense of personal commitment to action and service.',
    'learner_open_minded' => 'Respects the views, values and traditions of other individuals and cultures and is accustomed to seeking and considering a range of points of view.',
    'learner_balanced' => 'Understands physical, mental and personal well-being.',
    'learner_reflective' => 'Analyses own strength and weaknesses.',
];

$db = Database::getInstance();
$updated = 0;
$inserted = 0;

foreach ($seeds as $key => $value) {
    $row = $db->fetch('SELECT setting_key, setting_value FROM site_settings WHERE setting_key = ?', [$key]);
    if (!$row) {
        $db->query(
            'INSERT INTO site_settings (setting_key, setting_value, setting_type, setting_group) VALUES (?, ?, ?, ?)',
            [$key, $value, str_contains($key, 'message') || str_contains($key, 'overview') || str_contains($key, 'mission') || str_contains($key, 'vision') || str_contains($key, 'values') || str_contains($key, 'benefits') ? 'textarea' : 'text', str_starts_with($key, 'director') ? 'director' : (str_starts_with($key, 'learner') ? 'learner_profile' : 'about')]
        );
        echo "INSERT {$key}\n";
        $inserted++;
        continue;
    }

    if (trim((string) $row['setting_value']) === '') {
        $db->query('UPDATE site_settings SET setting_value = ? WHERE setting_key = ?', [$value, $key]);
        echo "FILL {$key}\n";
        $updated++;
    } else {
        echo "KEEP {$key}\n";
    }
}

// Prefer newest director photo if setting empty
$img = get_setting('director_image', '');
if ($img === '') {
    $files = glob(UPLOADS_PATH . '/director/*.{jpg,jpeg,png,webp,gif}', GLOB_BRACE) ?: [];
    if ($files) {
        usort($files, static fn($a, $b) => filemtime($b) <=> filemtime($a));
        $chosen = basename($files[0]);
        update_setting('director_image', $chosen);
        echo "SET director_image={$chosen}\n";
    }
}

clear_settings_cache();
echo "Done. inserted={$inserted} filled={$updated}\n";
