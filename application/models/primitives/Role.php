<?php
namespace application\models\primitives;

enum Role: string 
{
    case USER = "auth_user";
    case ADMIN = "admin";
    case MODERATOR = "moderator";
}