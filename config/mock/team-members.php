<?php

return [

    1 => [ // Project Beta
        'members' => [
            [
                'user_id' => 1, 
                'name' => 'User One',       
                'email' => 'user_1@gmail.com',    
                'role' => 'manager',   
                'status' => 'active', 
                'joinedAt' => '2026-02-03'
            ],
            [
                'user_id' => 7, 
                'name' => 'User Seven',  
                'email' => 'user_7@gmail.com',    
                'role' => 'team_lead', 
                'status' => 'active', 
                'joinedAt' => '2026-02-05'
            ],
            [
                'user_id' => 8, 
                'name' => 'User Eight',  
                'email' => 'user_8@gmail.com',    
                'role' => 'developer', 
                'status' => 'active', 
                'joinedAt' => '2026-02-10'
            ],
            [
                'user_id' => 9, 
                'name' => 'User Nine', 
                'email' => 'user_9@gmail.com',     
                'role' => 'designer',  
                'status' => 'active', 
                'joinedAt' => '2026-02-10'
            ],
        ],
        'pendingApprovals' => [],
    ],

    2 => [ // Project Gamma
        'members' => [
            [
                'user_id' => 1,  
                'name' => 'User One',      
                'email' => 'user_1@gmail.com',   
                'role' => 'manager',   
                'status' => 'active',   
                'joinedAt' => '2026-03-01'
            ],
            [
                'user_id' => 10,
                'name' => 'User Ten',
                'email' => 'user_10@gmail.com',
                'role' => 'team_lead',
                'status' => 'active',
                'joinedAt' => '2026-03-02'
            ],
            [
                'user_id' => 11,
                'name' => 'User Eleven',
                'email' => 'user_11@gmail.com',
                'role' => 'developer',
                'status' => 'active',
                'joinedAt' => '2026-03-05'
            ],
            [
                'user_id' => 12,
                'name' => 'User Twelve',
                'email' => 'user_12@gmail.com',
                'role' => 'developer',
                'status' => 'inactive',
                'joinedAt' => '2026-03-08'
            ],
        ],
        'pendingApprovals' => [
            [
                'user_id'      => 20,
                'name'         => 'User Twenty',
                'email'        => 'user_20@gmail.com',
                'requestedRole' => 'developer',
                'requestedBy'  => 'User Ten (Team Lead)',
                'requestedAt'  => '2026-08-05',
            ],
        ],
    ],

    3 => [ // Project Alpha
        'members' => [
            [
                'user_id' => 1,  
                'name' => 'User One',        
                'email' => 'user_1@gmail.com',     
                'role' => 'manager', 
                'status' => 'active', 
                'joinedAt' => '2026-01-12'
            ],
            [
                'user_id' => 13, 
                'name' => 'User Thirteen', 
                'email' => 'user_13@gmail.com', 
                'role' => 'developer',            
                'status' => 'active', 
                'joinedAt' => '2026-01-15'
            ],
            [
                'user_id' => 14, 
                'name' => 'User Fourteen', 
                'email' => 'user_14@gmail.com', 
                'role' => 'developer',           
                'status' => 'active', 
                'joinedAt' => '2026-01-15'
            ],
            [
                'user_id' => 15, 
                'name' => 'User Fifteen',  
                'email' => 'user_15@gmail.com',      
                'role' => 'designer',            
                'status' => 'active', 
                'joinedAt' => '2026-01-18'
            ],
            [
                'user_id' => 16, 
                'name' => 'User Sixteen',  
                'email' => 'user_16@gmail.com',      
                'role' => 'developer',           
                'status' => 'active', 
                'joinedAt' => '2026-01-20'
            ],
            [
                'user_id' => 17, 
                'name' => 'User Seventeen', 
                'email' => 'user_17@gmail.com',    
                'role' => 'designer',            
                'status' => 'inactive', 
                'joinedAt' => '2026-01-22'
            ],
            [
                'user_id' => 18, 
                'name' => 'User Eighteen',   
                'email' => 'user_18@gmail.com',     
                'role' => 'developer',           
                'status' => 'active', 
                'joinedAt' => '2026-01-25'
            ],
            [
                'user_id' => 19, 
                'name' => 'User Nineteen', 
                'email' => 'user_19@gmail.com', 
                'role' => 'client', 
                'status' => 'active', 
                'joinedAt' => '2026-01-25'
            ],
            [
                'user_id' => 31,
                'name' => 'User Thirty-One',
                'email' => 'user_31@gmail.com',
                'roles' => ['client'],
                'status' => 'active',
                'joinedAt' => '2026-02-01'
            ]
        ],
        'pendingApprovals' => [
            [
                'user_id'      => 23,
                'name'         => 'User Twenty-Three',
                'email'        => 'user_23@dgmail.com',
                'requestedRole' => 'designer',
                'requestedBy'  => 'User One (Manager)',
                'requestedAt'  => '2026-08-06',
            ],
        ],
    ],

    4 => [ // Marketing Site Redesign
        'members' => [
            [
                'user_id' => 1, 
                'name' => 'User One',   
                'email' => 'user_1@gmail.com', 
                'role' => 'manager',   
                'status' => 'active', 
                'joinedAt' => '2026-05-01'
            ],
            [
                'user_id' => 24, 
                'name' => 'User Twenty-Four', 
                'email' => 'user_24@gmail.com', 
                'role' => 'team_lead', 
                'status' => 'active', 
                'joinedAt' => '2026-05-02'
            ],
            [
                'user_id' => 25, 
                'name' => 'User Twenty-Five', 
                'email' => 'user_25@gmail.com', 
                'role' => 'designer', 
                'status' => 'active', 
                'joinedAt' => '2026-05-06'
            ],
        ],
        'pendingApprovals' => [],
    ],

    5 => [ // Project Delta - archived
        'members' => [
            [
                'user_id' => 1, 
                'name' => 'User One',    
                'email' => 'user_1@gmail.com', 
                'role' => 'manager',   
                'status' => 'active', 
                'joinedAt' => '2025-11-01'
            ],
            [
                'user_id' => 26, 
                'name' => 'User Twenty-Six', 
                'email' => 'user_26@gmail.com',  
                'role' => 'team_lead', 
                'status' => 'active', 
                'joinedAt' => '2025-11-02'
            ],
        ],
        'pendingApprovals' => [],
    ],

    6 => [ // Project Epsilon - closed
        'members' => [],
        'pendingApprovals' => [],
    ],
];