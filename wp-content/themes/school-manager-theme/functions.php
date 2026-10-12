<?php
add_action( 'wp_enqueue_scripts', function() {
    wp_enqueue_style( 'school-manager-theme-style', get_stylesheet_uri() );
} );

add_theme_support( 'title-tag' );

add_action( 'wp_enqueue_scripts', function() {
    if ( is_page('portal') ) {
        wp_enqueue_script( 'sm-portal-script', get_template_directory_uri() . '/portal.js', [], '1.0', true );
        wp_add_inline_script( 'sm-portal-script', '
            document.addEventListener("DOMContentLoaded", function() {
                const app = document.getElementById("sm-portal-app");
                if (!app) return;

                app.innerHTML = `
                    <div class="sm-sidebar">
                        <ul>
                            <li><a href="#dashboard" onclick="loadPage(\'dashboard\')">Dashboard</a></li>
                            <li><a href="#settings" onclick="loadPage(\'settings\')">General Settings</a></li>
                            <li><a href="#classes" onclick="loadPage(\'classes\')">Classes</a></li>
                            <li><a href="#subjects" onclick="loadPage(\'subjects\')">Subjects</a></li>
                            <li><a href="#students" onclick="loadPage(\'students\')">Students</a></li>
                            <li><a href="#employees" onclick="loadPage(\'employees\')">Employees</a></li>
                            <li><a href="#attendance" onclick="loadPage(\'attendance\')">Attendance</a></li>
                            <li><a href="#fees" onclick="loadPage(\'fees\')">Fees</a></li>
                            <li><a href="#accounts" onclick="loadPage(\'accounts\')">Accounts</a></li>
                            <li><a href="#timetable" onclick="loadPage(\'timetable\')">Timetable</a></li>
                            <li><a href="#homework" onclick="loadPage(\'homework\')">Homework</a></li>
                            <li><a href="#exams" onclick="loadPage(\'exams\')">Exams</a></li>
                            <li><a href="#payroll" onclick="loadPage(\'payroll\')">Payroll</a></li>
                            <li><a href="#messaging" onclick="loadPage(\'messaging\')">Messaging</a></li>
                            <li><a href="#certificates" onclick="loadPage(\'certificates\')">Certificates/IDs</a></li>
                            <li><a href="#reports" onclick="loadPage(\'reports\')">Reports</a></li>
                            <li><a href="'.wp_logout_url(site_url('/school-login/')).'">Log Out</a></li>
                        </ul>
                    </div>
                    <div class="sm-main-content">
                        <div class="sm-topbar">
                            <div id="sm-school-name-top">School Name</div>
                        </div>
                        <div class="sm-page-content" id="sm-page-content">
                            Loading...
                        </div>
                    </div>
                `;

                window.loadPage = function(page) {
                    const content = document.getElementById("sm-page-content");
                    content.innerHTML = "Loading...";

                    if (["classes", "subjects", "students", "employees", "attendance", "fees", "accounts", "timetable", "homework", "exams", "payroll", "messaging", "certificates", "reports"].includes(page)) {
                        content.innerHTML = `<h2>${page.charAt(0).toUpperCase() + page.slice(1)}</h2><p>Module stub implemented on backend. Frontend UI under construction.</p>`;
                        return;
                    }

                    fetch("'.rest_url('sm/v1/').'" + page, {
                        headers: { "X-WP-Nonce": "'.wp_create_nonce('wp_rest').'" }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (page === "dashboard") {
                            document.getElementById("sm-school-name-top").innerText = data.school_name;
                            content.innerHTML = `
                                <h2>Welcome, ${data.school_name}</h2>
                                <div style="display:flex; gap: 20px;">
                                    <div class="sm-card">Total Students: ${data.total_students}</div>
                                    <div class="sm-card">Total Employees: ${data.total_employees}</div>
                                    <div class="sm-card">Revenue: ${data.revenue}</div>
                                    <div class="sm-card">Profit: ${data.profit}</div>
                                </div>
                            `;
                        } else if (page === "settings") {
                            content.innerHTML = `<h2>General Settings</h2><p>Coming Soon...</p>`;
                        }
                    });
                };

                loadPage("dashboard");
            });
        ' );
    }
} );
