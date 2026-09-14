<?php

class DashboardController extends Controller {

    public function index(): void {
        $user           = currentUserContext();
        $projectContext = currentProjectContext();
        $notifications  = require __DIR__ . '/../../config/mock/notifications.php';

        $context = array_merge(
            [
                'pageTitle'     => 'Dashboard',
                'currentUser'   => $user,
                'currentRoute'  => '/dashboard',
                'unreadCount'   => $notifications['unreadCount'],
                'notifications' => $notifications['items'],
            ],
            $projectContext
        );

        if (empty($context['userProjects'])) {
            $this->render('pages/dashboard', array_merge($context, [
                'currentProjectId' => null,
                'icon'             => 'rocket',
                'heading'          => 'Ready to launch your first project?',
                'subtext'          => 'Create a project to start managing tasks, tracking progress, and collaborating with your team.',
                'ctaText'          => 'Create a Project',
                'ctaTrigger'       => 'project-wizard',
            ]));
            return;
        }

        $projectsList = projectsListForCurrentUser();
        $roles        = splitProjectRolesByClient($projectsList);

        if (empty($roles['other']) && !empty($roles['client'])) {
            $this->render('dashboard/client', array_merge($context, [
                'clientProjects' => buildClientProjectBundles($roles['client']),
            ]));
            return;
        }

        $clientProjects = !empty($roles['client'])
            ? buildClientProjectBundles($roles['client'])
            : [];

        if (userHasRoleAnywhere('manager')) {
            $managerData = require __DIR__ . '/../../config/mock/manager-dashboard.php';
            $this->render('dashboard/manager', array_merge($context, $managerData, [
                'clientProjects' => $clientProjects,
            ]));
            return;
        }

        if (userHasRoleAnywhere('team_lead')) {
            $teamLeadData = require __DIR__ . '/../../config/mock/teamlead-dashboard.php';
            $this->render('dashboard/teamlead', array_merge($context, $teamLeadData, [
                'projects'       => buildProjectRollupCards($roles['other']),
                'clientProjects' => $clientProjects,
            ]));
            return;
        }

        if (!empty($roles['other'])) {
            $contributorData = require __DIR__ . '/../../config/mock/contributor-dashboard.php';
            $this->render('dashboard/contributor', array_merge($context, $contributorData, [
                'projects'       => buildProjectRollupCards($roles['other']),
                'clientProjects' => $clientProjects,
            ]));
            return;
        }

        $this->render('pages/placeholder', array_merge($context, [
            'heading'        => 'Dashboard',
            'clientProjects' => $clientProjects,
        ]));
    }
}