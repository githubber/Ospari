<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
 $model = $this->model;
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Internal Mail</title>
        <style>
            table {
            max-width: 400px;
            background-color: transparent;
            border-collapse: collapse;
            border-spacing: 0;
          }
      .table {
        width: 400px;
        margin-bottom: 20px;
      }
      .table th,
      .table td {
        padding: 8px;
        line-height: 20px;
        text-align: left;
        vertical-align: top;
        border-top: 1px solid #dddddd;
      }
      .table th {
        font-weight: bold;
      }
      .table thead th {
        vertical-align: bottom;
      }
      .table caption + thead tr:first-child th,
      .table caption + thead tr:first-child td,
      .table colgroup + thead tr:first-child th,
      .table colgroup + thead tr:first-child td,
      .table thead:first-child tr:first-child th,
      .table thead:first-child tr:first-child td {
        border-top: 0;
      }
      .table tbody + tbody {
        border-top: 2px solid #dddddd;
      }
      .table .table {
        background-color: #ffffff;
      }
      .table-condensed th,
      .table-condensed td {
        padding: 4px 5px;
      }
      .table-bordered {
        border: 1px solid #dddddd;
        border-collapse: separate;
        *border-collapse: collapse;
        border-left: 0;
        -webkit-border-radius: 4px;
        -moz-border-radius: 4px;
        border-radius: 4px;
      }
      .table-bordered th,
      .table-bordered td {
        border-left: 1px solid #dddddd;
      }

      .table-bordered caption + tbody tr:first-child td,
      .table-bordered colgroup + tbody tr:first-child td,
      .table-bordered tbody:first-child tr:first-child td {
        border-top: 0;
      }
      .table-bordered tbody:first-child tr:first-child > td:first-child {
        -webkit-border-top-left-radius: 4px;
        -moz-border-radius-topleft: 4px;
        border-top-left-radius: 4px;
      }
      .table-bordered tbody:first-child tr:first-child > td:last-child {
        -webkit-border-top-right-radius: 4px;
        -moz-border-radius-topright: 4px;
        border-top-right-radius: 4px;
      }
      .table-bordered tbody:last-child tr:last-child > td:first-child,
      .table-bordered tfoot:last-child tr:last-child > td:first-child {
        -webkit-border-bottom-left-radius: 4px;
        -moz-border-radius-bottomleft: 4px;
        border-bottom-left-radius: 4px;
      }
      .table-bordered tbody:last-child tr:last-child > td:last-child,
      .table-bordered tfoot:last-child tr:last-child > td:last-child {
        -webkit-border-bottom-right-radius: 4px;
        -moz-border-radius-bottomright: 4px;
        border-bottom-right-radius: 4px;
      }
      .table-bordered tfoot + tbody:last-child tr:last-child td:first-child {
        -webkit-border-bottom-left-radius: 0;
        -moz-border-radius-bottomleft: 0;
        border-bottom-left-radius: 0;
      }
      .table-bordered tfoot + tbody:last-child tr:last-child td:last-child {
        -webkit-border-bottom-right-radius: 0;
        -moz-border-radius-bottomright: 0;
        border-bottom-right-radius: 0;
      }
      .table-bordered caption + tbody tr:first-child td:first-child,
      .table-bordered colgroup + tbody tr:first-child td:first-child {
        -webkit-border-top-left-radius: 4px;
        -moz-border-radius-topleft: 4px;
        border-top-left-radius: 4px;
      }
      .table-bordered caption + tbody tr:first-child td:last-child,
      .table-bordered colgroup + tbody tr:first-child td:last-child {
        -webkit-border-top-right-radius: 4px;
        -moz-border-radius-topright: 4px;
        border-top-right-radius: 4px;
      }
     
    </style>
    </head>
    <body>

    <table class="table table-striped table-bordered">
        <tbody>
            <tr>
                <td>
                    Name
                </td>
                <td>
                   <?= $model->name?> 
                </td>
            </tr>
            <tr>
                <td>
                    Appointment Date
                </td>
                <td>
                    <?= $model->appointment_day?> 
                </td>
            </tr>
            <tr>
                <td>
                    Appointment Time
                </td>
                <td>
                     <?= $model->appointment_time?> 
                </td>
            </tr>
            <tr>
                <td>
                    Company Name
                </td>
                <td>
                     <?= $model->company?> 
                </td>
            </tr>
            <tr>
                <td>
                    Email
                </td>
                <td>
                     <?= $model->email?> 
                </td>
            </tr>
            <tr>
                <td>
                    Phone Number
                </td>
                <td>
                     <?= $model->phone_nr?> 
                </td>
            </tr>
            <tr>
                <td>
                    City
                </td>
                <td>
                     <?= $model->city?> 
                </td>
            </tr>
        </tbody>    
    </table>
  </body>
</html>